<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_OLD_SLUG_META = '_okoyom_old_slug';

function okoyom_tracked_taxonomies(): array {
	return apply_filters(
		'okoyom_tracked_taxonomies',
		array_merge( array_keys( okoyom_filter_taxonomies() ), array( 'product_cat' ) )
	);
}

function okoyom_term_slug_before( int $term_id, ?string $slug = null ): string {
	static $slugs = array();

	if ( null !== $slug ) {
		$slugs[ $term_id ] = $slug;

		return $slug;
	}

	return $slugs[ $term_id ] ?? '';
}

add_action(
	'edit_terms',
	static function ( $term_id, $taxonomy ): void {
		if ( ! in_array( (string) $taxonomy, okoyom_tracked_taxonomies(), true ) ) {
			return;
		}

		$term = get_term( (int) $term_id, (string) $taxonomy );
		if ( $term instanceof WP_Term ) {
			okoyom_term_slug_before( (int) $term_id, $term->slug );
		}
	},
	10,
	2
);

add_action(
	'edited_term',
	static function ( $term_id, $tt_id, $taxonomy ): void {
		if ( ! in_array( (string) $taxonomy, okoyom_tracked_taxonomies(), true ) ) {
			return;
		}

		$before = okoyom_term_slug_before( (int) $term_id );
		if ( '' === $before ) {
			return;
		}

		$term = get_term( (int) $term_id, (string) $taxonomy );
		if ( ! $term instanceof WP_Term || $term->slug === $before ) {
			return;
		}

		okoyom_term_remember_slug( (int) $term_id, $before );
		delete_term_meta( (int) $term_id, OKOYOM_OLD_SLUG_META, $term->slug );
	},
	10,
	3
);

function okoyom_term_remember_slug( int $term_id, string $slug ): void {
	if ( '' === $slug ) {
		return;
	}

	$known = (array) get_term_meta( $term_id, OKOYOM_OLD_SLUG_META, false );
	if ( in_array( $slug, $known, true ) ) {
		return;
	}

	add_term_meta( $term_id, OKOYOM_OLD_SLUG_META, $slug );
}

function okoyom_taxonomy_by_rewrite( string $base ): string {
	foreach ( okoyom_tracked_taxonomies() as $taxonomy ) {
		$object = get_taxonomy( $taxonomy );
		if ( ! $object || empty( $object->rewrite['slug'] ) ) {
			continue;
		}

		if ( trim( (string) $object->rewrite['slug'], '/' ) === $base ) {
			return $taxonomy;
		}
	}

	return '';
}

function okoyom_old_term_slug_redirect(): void {
	if ( ! is_404() ) {
		return;
	}

	$path  = trim( (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' );
	$parts = array_values( array_filter( explode( '/', $path ) ) );

	if ( count( $parts ) < 2 ) {
		return;
	}

	$slug     = array_pop( $parts );
	$base     = implode( '/', $parts );
	$taxonomy = okoyom_taxonomy_by_rewrite( $base );

	if ( '' === $taxonomy || '' === $slug ) {
		return;
	}

	$found = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => 1,
			'fields'     => 'ids',
			'meta_key'   => OKOYOM_OLD_SLUG_META,
			'meta_value' => $slug,
		)
	);

	if ( is_wp_error( $found ) || ! $found ) {
		return;
	}

	$link = get_term_link( (int) $found[0], $taxonomy );
	if ( is_wp_error( $link ) ) {
		return;
	}

	$query = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_QUERY );
	if ( '' !== $query ) {
		$link .= ( false === strpos( $link, '?' ) ? '?' : '&' ) . $query;
	}

	wp_safe_redirect( $link, 301 );
	exit;
}

add_action( 'template_redirect', 'okoyom_old_term_slug_redirect', 7 );
