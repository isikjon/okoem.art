<?php

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'okoyom/v1',
			'/lead',
			array(
				'methods'             => 'POST',
				'callback'            => 'okoyom_rest_create_lead',

				'permission_callback' => '__return_true',
			)
		);
	}
);

function okoyom_rest_create_lead( WP_REST_Request $request ): WP_REST_Response {
	$type = sanitize_key( $request->get_param( 'type' ) ?: 'contact' );
	if ( ! array_key_exists( $type, OKOYOM_LEAD_TYPES ) ) {
		$type = 'contact';
	}

	$name    = sanitize_text_field( (string) $request->get_param( 'name' ) );
	$phone   = sanitize_text_field( (string) $request->get_param( 'phone' ) );
	$email   = sanitize_email( (string) $request->get_param( 'email' ) );
	$message = sanitize_textarea_field( (string) $request->get_param( 'message' ) );

	if ( '' === $name || ( '' === $phone && '' === $email ) ) {
		return new WP_REST_Response(
			array( 'ok' => false, 'error' => 'Нужно имя и хотя бы один контакт: телефон или email.' ),
			422
		);
	}

	$lead_id = wp_insert_post(
		array(
			'post_type'   => OKOYOM_LEAD_CPT,
			'post_status' => 'oko_new',
			'post_title'  => sprintf(
				'%s — %s — %s',
				OKOYOM_LEAD_TYPES[ $type ],
				$name,
				wp_date( 'd.m.Y H:i' )
			),
		)
	);

	if ( ! $lead_id || is_wp_error( $lead_id ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'Не удалось сохранить заявку.' ), 500 );
	}

	update_post_meta( $lead_id, '_okoyom_lead_type', $type );
	update_post_meta( $lead_id, '_okoyom_environment', okoyom_environment() );
	update_post_meta( $lead_id, '_okoyom_name', $name );
	update_post_meta( $lead_id, '_okoyom_phone', $phone );
	update_post_meta( $lead_id, '_okoyom_email', $email );
	update_post_meta( $lead_id, '_okoyom_message', $message );

	$product_id = absint( $request->get_param( 'product_id' ) );
	if ( $product_id ) {
		update_post_meta( $lead_id, '_okoyom_product_id', $product_id );
		update_post_meta( $lead_id, '_okoyom_product_sku', sanitize_text_field( (string) $request->get_param( 'sku' ) ) );
		update_post_meta( $lead_id, '_okoyom_product_url', esc_url_raw( (string) $request->get_param( 'product_url' ) ) );
	}

	$cart  = $request->get_param( 'cart' );
	$value = 0;
	if ( is_array( $cart ) && $cart ) {
		$clean = array();
		foreach ( array_slice( $cart, 0, 50 ) as $item ) {
			$row = array(
				'product_id' => absint( $item['productId'] ?? 0 ),
				'title'      => sanitize_text_field( (string) ( $item['title'] ?? '' ) ),
				'sku'        => sanitize_text_field( (string) ( $item['sku'] ?? '' ) ),
				'w'          => absint( $item['w'] ?? 0 ),
				'h'          => absint( $item['h'] ?? 0 ),
				'material'   => sanitize_text_field( (string) ( $item['material'] ?? '' ) ),
				'area'       => round( (float) ( $item['area'] ?? 0 ), 2 ),
				'price'      => absint( $item['price'] ?? 0 ),
			);
			$value  += $row['price'];
			$clean[] = $row;
		}
		update_post_meta( $lead_id, '_okoyom_cart', wp_json_encode( $clean, JSON_UNESCAPED_UNICODE ) );
		update_post_meta( $lead_id, '_okoyom_value', $value );
	}

	$attribution = $request->get_param( 'attribution' );
	if ( is_array( $attribution ) ) {
		foreach ( OKOYOM_ATTRIBUTION_KEYS as $key ) {
			if ( ! empty( $attribution[ $key ] ) ) {
				update_post_meta( $lead_id, '_okoyom_' . $key, sanitize_text_field( (string) $attribution[ $key ] ) );
			}
		}
	}

	okoyom_send_lead_email( $lead_id );

	return new WP_REST_Response( array( 'ok' => true, 'id' => $lead_id ), 201 );
}

function okoyom_send_lead_email( int $lead_id ): void {
	$to = function_exists( 'okoyom_option' ) ? okoyom_option( 'lead_email' ) : '';
	if ( ! is_email( $to ) ) {
		return;
	}

	wp_update_post( array( 'ID' => $lead_id, 'post_status' => 'oko_sending' ) );

	$attempts = (int) get_post_meta( $lead_id, '_okoyom_attempts', true );
	update_post_meta( $lead_id, '_okoyom_attempts', $attempts + 1 );

	$type  = (string) get_post_meta( $lead_id, '_okoyom_lead_type', true );
	$env   = (string) get_post_meta( $lead_id, '_okoyom_environment', true );
	$lines = array(
		'Тип: ' . ( OKOYOM_LEAD_TYPES[ $type ] ?? $type ),
		'Имя: ' . get_post_meta( $lead_id, '_okoyom_name', true ),
		'Телефон: ' . get_post_meta( $lead_id, '_okoyom_phone', true ),
		'Email: ' . get_post_meta( $lead_id, '_okoyom_email', true ),
		'Сообщение: ' . get_post_meta( $lead_id, '_okoyom_message', true ),
	);

	$cart = get_post_meta( $lead_id, '_okoyom_cart', true );
	if ( $cart ) {
		$lines[] = '';
		$lines[] = 'Состав корзины:';
		foreach ( (array) json_decode( (string) $cart, true ) as $item ) {
			$lines[] = sprintf(
				'— %s (%s), %d×%d см, %s, %.2f м², %d ₽',
				$item['title'],
				$item['sku'],
				$item['w'],
				$item['h'],
				$item['material'],
				$item['area'],
				$item['price']
			);
		}
		$lines[] = 'Итого: ' . get_post_meta( $lead_id, '_okoyom_value', true ) . ' ₽';
	}

	$type_label = OKOYOM_LEAD_TYPES[ $type ] ?? $type;
	$subject    = sprintf(
		'%sЗаявка — %s: %s',
		'prod' === $env ? '' : '[' . strtoupper( $env ) . '] ',
		$type_label,
		get_the_title( $lead_id )
	);

	$sent = wp_mail( $to, $subject, implode( "\n", $lines ) );

	if ( $sent ) {
		wp_update_post( array( 'ID' => $lead_id, 'post_status' => 'oko_sent' ) );
		update_post_meta( $lead_id, '_okoyom_sent_at', time() );
	} else {
		wp_update_post( array( 'ID' => $lead_id, 'post_status' => 'oko_failed' ) );
		update_post_meta( $lead_id, '_okoyom_last_error', 'wp_mail вернул false — SMTP недоступен или не настроен' );
	}
}
