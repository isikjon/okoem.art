<?php

defined( 'ABSPATH' ) || exit;

get_header();

$okoyom_map  = okoyom_static_pages();
$okoyom_slug = get_post_field( 'post_name', get_queried_object_id() );

if ( 'cart' === $okoyom_slug ) {
	echo okoyom_render_cart_page();
} elseif ( 'favorites' === $okoyom_slug ) {
	echo okoyom_render_favorites_page();
} elseif ( 'search' === $okoyom_slug ) {

	$GLOBALS['okoyom_search_query'] = sanitize_text_field( wp_unslash( $_GET['q'] ?? '' ) );
	okoyom_static_part( 'search' );
} elseif ( ! isset( $okoyom_map[ $okoyom_slug ] ) || ! okoyom_static_part( $okoyom_map[ $okoyom_slug ] ) ) {
	while ( have_posts() ) {
		the_post();
		echo '<div class="container"><div class="staticContent">';
		the_content();
		echo '</div></div>';
	}
}

get_footer();
