<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_RETRY_HOOK   = 'okoyom_lead_retry';
const OKOYOM_MAX_ATTEMPTS = 5;

add_filter(
	'cron_schedules',
	function ( array $schedules ): array {
		$schedules['okoyom_half_hour'] = array(
			'interval' => 30 * MINUTE_IN_SECONDS,
			'display'  => 'Каждые 30 минут (Окоём)',
		);

		return $schedules;
	}
);

add_action(
	'init',
	function () {
		if ( ! wp_next_scheduled( OKOYOM_RETRY_HOOK ) ) {
			wp_schedule_event( time() + MINUTE_IN_SECONDS, 'okoyom_half_hour', OKOYOM_RETRY_HOOK );
		}
	}
);

add_action(
	OKOYOM_RETRY_HOOK,
	function () {
		$failed = get_posts(
			array(
				'post_type'      => OKOYOM_LEAD_CPT,
				'post_status'    => 'oko_failed',
				'posts_per_page' => 20,
				'fields'         => 'ids',
			)
		);

		foreach ( $failed as $lead_id ) {
			$attempts = (int) get_post_meta( $lead_id, '_okoyom_attempts', true );
			if ( $attempts >= OKOYOM_MAX_ATTEMPTS ) {
				continue;
			}
			okoyom_send_lead_email( (int) $lead_id );
		}
	}
);

add_filter(
	'manage_' . OKOYOM_LEAD_CPT . '_posts_columns',
	function ( array $columns ): array {
		return array(
			'cb'              => $columns['cb'] ?? '',
			'title'           => 'Заявка',
			'okoyom_status'   => 'Статус',
			'okoyom_contact'  => 'Контакты',
			'okoyom_value'    => 'Сумма',
			'okoyom_env'      => 'Окружение',
			'okoyom_attempts' => 'Попытки',
			'date'            => 'Дата',
		);
	}
);

add_action(
	'manage_' . OKOYOM_LEAD_CPT . '_posts_custom_column',
	function ( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'okoyom_status':
				$status = get_post_status( $post_id );
				$label  = OKOYOM_LEAD_STATUSES[ $status ] ?? $status;
				$color  = 'oko_failed' === $status ? '#d63638' : ( 'oko_sent' === $status ? '#00a32a' : '#996800' );
				printf( '<strong style="color:%s">%s</strong>', esc_attr( $color ), esc_html( $label ) );

				if ( 'oko_failed' === $status ) {
					$url = wp_nonce_url(
						admin_url( 'admin-post.php?action=okoyom_resend_lead&lead=' . $post_id ),
						'okoyom_resend_' . $post_id
					);
					printf( '<br><a href="%s">Переотправить</a>', esc_url( $url ) );

					$error = get_post_meta( $post_id, '_okoyom_last_error', true );
					if ( $error ) {
						printf( '<br><span style="color:#646970">%s</span>', esc_html( $error ) );
					}
				}
				break;

			case 'okoyom_contact':
				echo esc_html( trim( get_post_meta( $post_id, '_okoyom_phone', true ) . ' ' . get_post_meta( $post_id, '_okoyom_email', true ) ) );
				break;

			case 'okoyom_value':
				$value = get_post_meta( $post_id, '_okoyom_value', true );
				echo $value ? esc_html( number_format( (float) $value, 0, ',', ' ' ) . ' ₽' ) : '—';
				break;

			case 'okoyom_env':
				echo esc_html( get_post_meta( $post_id, '_okoyom_environment', true ) );
				break;

			case 'okoyom_attempts':
				echo (int) get_post_meta( $post_id, '_okoyom_attempts', true );
				break;
		}
	},
	10,
	2
);

add_action(
	'admin_post_okoyom_resend_lead',
	function () {
		$lead_id = absint( $_GET['lead'] ?? 0 );

		if ( ! $lead_id
			|| ! current_user_can( 'edit_posts' )
			|| ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ?? '' ), 'okoyom_resend_' . $lead_id ) ) {
			wp_die( 'Недостаточно прав.' );
		}

		okoyom_send_lead_email( $lead_id );

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . OKOYOM_LEAD_CPT ) );
		exit;
	}
);
