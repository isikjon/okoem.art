<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_LEAD_CPT = 'oko_lead';

const OKOYOM_LEAD_STATUSES = array(
	'oko_new'     => 'NEW',
	'oko_sending' => 'SENDING',
	'oko_sent'    => 'SENT',
	'oko_failed'  => 'FAILED',
);

const OKOYOM_LEAD_TYPES = array(
	'cart_request'      => 'Запросить расчёт / предзаказ',
	'product_query'     => 'Задать вопрос по товару',
	'companion_request' => 'Запрос фоновых обоев',
	'contact'           => 'Обращение с сайта',
	'visualization'     => 'Запросить визуализацию',
	'designer_terms'    => 'Условия для дизайнеров',
	'consultation'      => 'Получить консультацию',
);

const OKOYOM_ATTRIBUTION_KEYS = array(
	'utm_source',
	'utm_medium',
	'utm_campaign',
	'utm_content',
	'utm_term',
	'yclid',
	'vkclid',
	'referer',
	'landing_page_url',
	'current_page_url',
);

function okoyom_register_lead_cpt(): void {
	register_post_type(
		OKOYOM_LEAD_CPT,
		array(
			'labels'          => array(
				'name'          => 'Заявки',
				'singular_name' => 'Заявка',
				'edit_item'     => 'Заявка',
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email-alt',

			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
			'supports'        => array( 'title' ),
			'show_in_rest'    => false,
		)
	);

	foreach ( OKOYOM_LEAD_STATUSES as $status => $label ) {
		register_post_status(
			$status,
			array(
				'label'                     => $label,
				'public'                    => false,
				'internal'                  => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,

				'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>' ),
			)
		);
	}

	register_post_meta(
		OKOYOM_LEAD_CPT,
		'_okoyom_lead_type',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_key',
			'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
		)
	);

	register_post_meta(
		OKOYOM_LEAD_CPT,
		'_okoyom_environment',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_key',
			'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
		)
	);

	foreach ( OKOYOM_ATTRIBUTION_KEYS as $key ) {
		register_post_meta(
			OKOYOM_LEAD_CPT,
			'_okoyom_' . $key,
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
}

add_action( 'init', 'okoyom_register_lead_cpt' );

function okoyom_environment(): string {
	if ( defined( 'OKOYOM_ENV' ) && in_array( OKOYOM_ENV, array( 'prod', 'staging', 'local' ), true ) ) {
		return OKOYOM_ENV;
	}

	return 'local';
}
