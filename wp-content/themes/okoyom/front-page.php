<?php

defined( 'ABSPATH' ) || exit;

get_header();

ob_start();
get_template_part( 'template-parts/static/index' );
$okoyom_home = (string) ob_get_clean();

$okoyom_featured = function_exists( 'okoyom_featured_products' ) ? okoyom_featured_products( 4 ) : array();

if ( ! $okoyom_featured ) {
	$okoyom_featured = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
		)
	);
}

if ( $okoyom_featured ) {
	ob_start();
	foreach ( $okoyom_featured as $okoyom_pid ) {
		okoyom_catalog_card( get_post( $okoyom_pid ) );
	}
	$okoyom_cards = (string) ob_get_clean();

	if ( function_exists( 'okoyom_replace_block_inner' ) ) {
		$okoyom_home = okoyom_replace_block_inner(
			$okoyom_home,
			'<div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2">',
			$okoyom_cards
		);
	}
}

echo $okoyom_home;

okoyom_static_scripts( 'index' );

get_footer();
