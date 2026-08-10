<?php

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );

		add_theme_support( 'woocommerce' );

		register_nav_menus(
			array(
				'primary' => 'Главное меню',
				'footer'  => 'Меню в подвале',
			)
		);
	}
);

const OKOYOM_FLUSH_FOOTER_PAGES = array( 'about', 'buyers', 'designers', 'contacts' );

add_filter(
	'body_class',
	function ( array $classes ): array {
		$slug = get_post_field( 'post_name', get_queried_object_id() );

		if ( in_array( $slug, OKOYOM_FLUSH_FOOTER_PAGES, true ) ) {
			$classes[] = 'has-flush-footer';
		}

		return $classes;
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);
