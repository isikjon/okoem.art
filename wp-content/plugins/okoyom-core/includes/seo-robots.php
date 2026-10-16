<?php

defined( 'ABSPATH' ) || exit;

function okoyom_public_hosts(): array {
	return apply_filters( 'okoyom_public_hosts', array( 'okoem.art', 'www.okoem.art' ) );
}

function okoyom_is_public_host(): bool {
	$host = strtolower( (string) ( $_SERVER['HTTP_HOST'] ?? '' ) );
	$host = (string) preg_replace( '~:\d+$~', '', $host );

	return '' !== $host && in_array( $host, okoyom_public_hosts(), true );
}

function okoyom_filter_params(): string {
	$params = function_exists( 'okoyom_filter_taxonomies_map' )
		? array_keys( okoyom_filter_taxonomies_map() )
		: array( 'collection', 'series', 'subject', 'color' );

	return implode( '&', $params );
}

function okoyom_query_is_filtered(): bool {
	$keys = function_exists( 'okoyom_filter_taxonomies_map' )
		? array_keys( okoyom_filter_taxonomies_map() )
		: array( 'collection', 'series', 'subject', 'color' );

	foreach ( array_merge( $keys, array( 'q', 'sort', 'tab' ) ) as $key ) {
		if ( ! empty( $_GET[ $key ] ) ) {
			return true;
		}
	}

	return false;
}

function okoyom_catalog_total(): int {
	static $total = null;
	if ( null === $total ) {
		$counts = wp_count_posts( 'product' );
		$total  = (int) ( $counts->publish ?? 0 );
	}

	return $total;
}

function okoyom_term_product_count( string $taxonomy, int $term_id ): int {
	static $cache = array();
	$key = $taxonomy . ':' . $term_id;

	if ( ! isset( $cache[ $key ] ) ) {
		$cache[ $key ] = count(
			get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'tax_query'      => array(
						array(
							'taxonomy'         => $taxonomy,
							'field'            => 'term_id',
							'terms'            => $term_id,
							'include_children' => false,
						),
					),
				)
			)
		);
	}

	return $cache[ $key ];
}

function okoyom_term_duplicates_catalog( string $taxonomy, int $term_id ): bool {
	$total = okoyom_catalog_total();
	if ( $total < 1 ) {
		return false;
	}

	return okoyom_term_product_count( $taxonomy, $term_id ) >= $total;
}

function okoyom_archive_duplicates_catalog(): bool {
	if ( ! is_tax( array( 'product_cat', 'oko_collection', 'oko_series', 'oko_subject', 'oko_color' ) ) ) {
		return false;
	}

	$term = get_queried_object();

	return $term instanceof WP_Term && okoyom_term_duplicates_catalog( $term->taxonomy, $term->term_id );
}

function okoyom_robots_meta( array $robots ): array {
	if ( ! okoyom_is_public_host() ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
		unset( $robots['index'], $robots['follow'] );

		return $robots;
	}

	if ( okoyom_query_is_filtered() || okoyom_archive_duplicates_catalog() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['index'], $robots['nofollow'] );
	}

	return $robots;
}
add_filter( 'wp_robots', 'okoyom_robots_meta', 99 );

function okoyom_rank_math_robots( $robots ) {
	if ( ! is_array( $robots ) ) {
		return $robots;
	}

	if ( ! okoyom_is_public_host() ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'nofollow';

		return $robots;
	}

	if ( okoyom_query_is_filtered() || okoyom_archive_duplicates_catalog() ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
	}

	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'okoyom_rank_math_robots', 99 );

function okoyom_robots_header(): void {
	if ( headers_sent() ) {
		return;
	}

	if ( ! okoyom_is_public_host() ) {
		header( 'X-Robots-Tag: noindex, nofollow', true );

		return;
	}

	if ( okoyom_query_is_filtered() || okoyom_archive_duplicates_catalog() ) {
		header( 'X-Robots-Tag: noindex, follow', true );
	}
}
add_action( 'send_headers', 'okoyom_robots_header' );

function okoyom_robots_txt( $output, $public ) {
	if ( ! okoyom_is_public_host() ) {
		return "User-agent: *\nDisallow: /\n";
	}

	$params = okoyom_filter_params();
	if ( '' !== $params && false === strpos( (string) $output, 'Clean-param:' ) ) {
		$output  = rtrim( (string) $output ) . "\n\nClean-param: " . $params . " /catalog/\n";
		$output .= "Clean-param: room /freski/\n";
	}

	return $output;
}
add_filter( 'robots_txt', 'okoyom_robots_txt', 99, 2 );

add_filter(
	'rank_math/sitemap/entry',
	static function ( $url, $type, $object ) {
		if ( 'term' !== $type || ! $object instanceof WP_Term ) {
			return $url;
		}

		return okoyom_term_duplicates_catalog( $object->taxonomy, $object->term_id ) ? array() : $url;
	},
	10,
	3
);

add_filter(
	'rank_math/sitemap/exclude_taxonomy',
	static function ( $exclude, $taxonomy ) {
		if ( $exclude || ! in_array( $taxonomy, array( 'oko_collection', 'oko_series', 'oko_subject', 'oko_color' ), true ) ) {
			return $exclude;
		}

		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'fields' => 'ids' ) );
		if ( is_wp_error( $terms ) || ! $terms ) {
			return true;
		}

		foreach ( $terms as $term_id ) {
			if ( ! okoyom_term_duplicates_catalog( $taxonomy, (int) $term_id ) ) {
				return $exclude;
			}
		}

		return true;
	},
	10,
	2
);
