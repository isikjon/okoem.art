<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_NOINDEX_PAGES = array( 'search', 'cart', 'favorites', 'thanks' );

add_filter(
	'wp_robots',
	function ( array $robots ): array {
		$slug = get_post_field( 'post_name', get_queried_object_id() );

		if ( is_page() && in_array( $slug, OKOYOM_NOINDEX_PAGES, true ) ) {
			$robots['noindex'] = true;

			$robots['follow'] = 'search' === $slug;
			if ( 'search' !== $slug ) {
				$robots['nofollow'] = true;
			}
		}

		$filter_params = array( 'collection', 'series', 'subject', 'color' );
		$has_filter    = false;
		foreach ( $filter_params as $param ) {
			if ( ! empty( $_GET[ $param ] ) ) {
				$has_filter = true;
				break;
			}
		}
		if ( $has_filter && ( is_page( 'catalog' ) || is_post_type_archive( 'product' ) ) ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['nofollow'] );
		}

		return $robots;
	}
);

add_filter(
	'wp_sitemaps_posts_query_args',
	function ( array $args, string $post_type ): array {
		if ( 'page' !== $post_type ) {
			return $args;
		}

		$exclude = array();
		foreach ( OKOYOM_NOINDEX_PAGES as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$exclude[] = $page->ID;
			}
		}
		$args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), $exclude );

		return $args;
	},
	10,
	2
);

function okoyom_jsonld( array $data ): void {
	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
	);
}

add_action(
	'wp_head',
	function () {

		okoyom_jsonld(
			array(
				'@context' => 'https://schema.org',
				'@type'    => 'Organization',
				'name'     => 'ОКОЁМ',
				'url'      => home_url( '/' ),
				'logo'     => OKOYOM_ASSETS_URI . '/img/logoHeader.svg',
			)
		);
		okoyom_jsonld(
			array(
				'@context' => 'https://schema.org',
				'@type'    => 'WebSite',
				'name'     => get_bloginfo( 'name' ),
				'url'      => home_url( '/' ),
			)
		);

		if ( is_page() && 'contacts' === get_post_field( 'post_name', get_queried_object_id() ) ) {
			okoyom_jsonld(
				array(
					'@context'  => 'https://schema.org',
					'@type'     => 'LocalBusiness',
					'name'      => 'ОКОЁМ',
					'url'       => get_permalink(),
					'telephone' => '+7 (495) 123-45-67',
					'address'   => array(
						'@type'           => 'PostalAddress',
						'addressLocality' => 'Санкт-Петербург',
						'streetAddress'   => 'ул. Полевая Сабировская, 54А, ТК «Интерио», 4 этаж, секция 454',
					),
					'sameAs'    => array(
						'https://www.instagram.com/okoem.art',
						'https://ru.pinterest.com/okoemart',
						'https://vk.com/okoem_art',
						'https://yandex.ru/rythm/businesses/@okoem.art',
					),
				)
			);
		}

		if ( is_singular( 'product' ) ) {
			$product = get_queried_object();
			$price   = okoyom_product_base_price( $product->ID );

			okoyom_jsonld(
				array(
					'@context'    => 'https://schema.org',
					'@type'       => 'Product',
					'name'        => get_the_title( $product ),
					'sku'         => (string) get_post_meta( $product->ID, '_sku', true ),
					'description' => get_the_excerpt( $product ),
					'image'       => get_the_post_thumbnail_url( $product, 'large' ) ?: '',
					'offers'      => array(
						'@type'         => 'Offer',
						'price'         => $price,
						'priceCurrency' => 'RUB',

						'availability'  => 'https://schema.org/InStock',
						'url'           => get_permalink( $product ),
					),
				)
			);

			okoyom_jsonld(
				array(
					'@context'        => 'https://schema.org',
					'@type'           => 'BreadcrumbList',
					'itemListElement' => array(
						array(
							'@type'    => 'ListItem',
							'position' => 1,
							'name'     => 'Главная',
							'item'     => home_url( '/' ),
						),
						array(
							'@type'    => 'ListItem',
							'position' => 2,
							'name'     => 'Каталог',
							'item'     => home_url( '/catalog/' ),
						),
						array(
							'@type'    => 'ListItem',
							'position' => 3,
							'name'     => get_the_title( $product ),
							'item'     => get_permalink( $product ),
						),
					),
				)
			);
		}
	},
	5
);

add_action(
	'template_redirect',
	function () {
		if ( empty( $_GET['s'] ) || ! empty( $_GET['q'] ) ) {
			return;
		}
		$query = sanitize_text_field( wp_unslash( $_GET['s'] ) );
		wp_safe_redirect( home_url( '/search/' ) . ( '' !== $query ? '?q=' . rawurlencode( $query ) : '' ), 301 );
		exit;
	}
);
