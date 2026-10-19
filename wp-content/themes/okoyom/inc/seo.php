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

add_filter(
	'rank_math/snippet/rich_snippet_product_entity',
	function ( $entity ) {
		$product = get_queried_object();
		if ( ! $product instanceof WP_Post || 'product' !== $product->post_type ) {
			return $entity;
		}

		$price  = okoyom_product_base_price( $product->ID );
		$series = function_exists( 'okoyom_product_series_name' ) ? okoyom_product_series_name( $product->ID ) : '';
		$images = wp_list_pluck( okoyom_product_slide_items( $product->ID ), 'src' );

		$entity['name']        = function_exists( 'okoyom_product_h1' )
			? okoyom_crumb_text( okoyom_product_h1( $product->ID ) )
			: okoyom_crumb_text( get_the_title( $product ) );
		$entity['sku']         = (string) get_post_meta( $product->ID, '_sku', true );
		$entity['brand']       = array( '@type' => 'Brand', 'name' => 'ОКОЁМ' );
		$entity['description'] = okoyom_crumb_text( get_the_excerpt( $product ) );

		if ( $images ) {
			$entity['image'] = count( $images ) > 1 ? array_values( $images ) : reset( $images );
		}

		if ( '' !== $series ) {
			$entity['category'] = okoyom_crumb_text( $series );
		}

		if ( $price > 0 ) {
			$entity['offers'] = array(
				'@type'              => 'Offer',
				'url'                => get_permalink( $product ),
				'price'              => (string) $price,
				'priceCurrency'      => 'RUB',
				'availability'       => 'https://schema.org/InStock',
				'priceSpecification' => array(
					'@type'                 => 'UnitPriceSpecification',
					'price'                 => (string) $price,
					'priceCurrency'         => 'RUB',
					'referenceQuantity'     => array(
						'@type'    => 'QuantitativeValue',
						'value'    => '1',
						'unitCode' => 'MTK',
					),
				),
			);
		} else {
			unset( $entity['offers'] );
		}

		return $entity;
	},
	20
);
