<?php

defined( 'ABSPATH' ) || exit;

function okoyom_asset_version( string $relative_path ): string {
	$file = OKOYOM_DIR . '/assets/' . ltrim( $relative_path, '/' );

	return file_exists( $file ) ? (string) filemtime( $file ) : OKOYOM_VERSION;
}

function okoyom_asset_uri( string $relative_path ): string {
	return OKOYOM_ASSETS_URI . '/' . ltrim( $relative_path, '/' );
}

function okoyom_vendor( string $relative_path, string $cdn_fallback ): string {
	$file = OKOYOM_DIR . '/assets/vendor/' . ltrim( $relative_path, '/' );

	return file_exists( $file ) ? okoyom_asset_uri( 'vendor/' . $relative_path ) : $cdn_fallback;
}

const OKOYOM_FONTS_FALLBACK = 'https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap';

function okoyom_fonts_url(): string {
	return file_exists( OKOYOM_DIR . '/assets/vendor/fonts/fonts.css' )
		? OKOYOM_ASSETS_URI . '/vendor/fonts/fonts.css'
		: OKOYOM_FONTS_FALLBACK;
}

add_filter(
	'wp_resource_hints',
	function ( array $hints, string $relation ): array {
		if ( 'preconnect' === $relation && ! file_exists( OKOYOM_DIR . '/assets/vendor/fonts/fonts.css' ) ) {
			$hints[] = 'https://fonts.googleapis.com';
			$hints[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}

		return $hints;
	},
	10,
	2
);

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action(
	'wp_enqueue_scripts',
	function () {
		foreach ( array( 'woocommerce', 'wc-add-to-cart', 'wc-cart-fragments', 'sourcebuster-js', 'wc-order-attribution' ) as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}

		foreach ( array( 'wc-blocks-style', 'wc-blocks-vendors-style', 'wc-block-style' ) as $handle ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
	},
	99
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'okoyom-fonts', okoyom_fonts_url(), array(), null );

		wp_enqueue_style(
			'okoyom-vendor-aos',
			okoyom_vendor( 'aos/aos.css', 'https://unpkg.com/aos@2.3.1/dist/aos.css' ),
			array(),
			'2.3.1'
		);
		wp_enqueue_style(
			'okoyom-vendor-swiper',
			okoyom_vendor( 'swiper/swiper-bundle.min.css', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css' ),
			array(),
			'12'
		);

		wp_enqueue_style(
			'okoyom-base',
			okoyom_asset_uri( 'style.css' ),
			array( 'okoyom-vendor-aos', 'okoyom-vendor-swiper' ),
			okoyom_asset_version( 'style.css' )
		);

		wp_enqueue_style(
			'okoyom-adapt',
			okoyom_asset_uri( 'adapt.css' ),
			array( 'okoyom-base' ),
			okoyom_asset_version( 'adapt.css' )
		);

		wp_enqueue_style(
			'okoyom-theme',
			okoyom_asset_uri( 'theme.css' ),
			array( 'okoyom-adapt' ),
			okoyom_asset_version( 'theme.css' )
		);

		wp_enqueue_script(
			'okoyom-vendor-aos',
			okoyom_vendor( 'aos/aos.js', 'https://unpkg.com/aos@2.3.1/dist/aos.js' ),
			array(),
			'2.3.1',
			true
		);
		wp_enqueue_script(
			'okoyom-vendor-swiper',
			okoyom_vendor( 'swiper/swiper-bundle.min.js', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js' ),
			array(),
			'12',
			true
		);

		wp_enqueue_script(
			'okoyom-app',
			okoyom_asset_uri( 'app.js' ),
			array( 'jquery', 'okoyom-vendor-aos', 'okoyom-vendor-swiper' ),
			okoyom_asset_version( 'app.js' ),
			true
		);

		wp_enqueue_script(
			'okoyom-theme',
			okoyom_asset_uri( 'theme.js' ),
			array(),
			okoyom_asset_version( 'theme.js' ),
			true
		);

		wp_localize_script(
			'okoyom-app',
			'okoyomData',
			array(
				'restUrl' => esc_url_raw( rest_url( 'okoyom/v1/' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}
);
