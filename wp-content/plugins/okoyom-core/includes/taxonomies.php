<?php

defined( 'ABSPATH' ) || exit;

function okoyom_filter_taxonomies(): array {
	return array(
		'oko_collection' => array(
			'labels' => array( 'Коллекция', 'Коллекции' ),
			'rewrite' => 'collection',
		),
		'oko_series'     => array(
			'labels' => array( 'Серия', 'Серии' ),
			'rewrite' => 'series',
		),
		'oko_subject'    => array(
			'labels' => array( 'Сюжет', 'Сюжеты' ),
			'rewrite' => 'subject',
		),
		'oko_color'      => array(
			'labels' => array( 'Цвет', 'Цвета' ),
			'rewrite' => 'color',
		),
	);
}

function okoyom_register_taxonomies(): void {
	foreach ( okoyom_filter_taxonomies() as $slug => $config ) {
		list( $singular, $plural ) = $config['labels'];

		$object_types = 'oko_series' === $slug
			? array( 'product' )
			: array( 'product', 'oko_inspiration' );

		register_taxonomy(
			$slug,
			$object_types,
			array(
				'labels'            => array(
					'name'          => $plural,
					'singular_name' => $singular,
					'menu_name'     => $plural,
				),

				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,

				'rewrite'           => array(
					'slug'       => $config['rewrite'],
					'with_front' => false,
				),
			)
		);
	}
}

add_action( 'init', 'okoyom_register_taxonomies' );
