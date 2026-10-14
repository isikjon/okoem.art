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

function okoyom_robots_meta( array $robots ): array {
	if ( okoyom_is_public_host() ) {
		return $robots;
	}

	$robots['noindex']  = true;
	$robots['nofollow'] = true;
	unset( $robots['index'], $robots['follow'] );

	return $robots;
}
add_filter( 'wp_robots', 'okoyom_robots_meta', 99 );

function okoyom_rank_math_robots( $robots ) {
	if ( okoyom_is_public_host() || ! is_array( $robots ) ) {
		return $robots;
	}

	$robots['index']  = 'noindex';
	$robots['follow'] = 'nofollow';

	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'okoyom_rank_math_robots', 99 );

function okoyom_robots_header(): void {
	if ( okoyom_is_public_host() || headers_sent() ) {
		return;
	}

	header( 'X-Robots-Tag: noindex, nofollow', true );
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
