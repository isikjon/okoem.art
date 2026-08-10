<?php

defined( 'ABSPATH' ) || exit;

get_header();

$okoyom_product = get_queried_object();

if ( $okoyom_product instanceof WP_Post ) {
	echo okoyom_render_product_page( $okoyom_product );
} else {
	okoyom_static_part( 'card' );
}

get_footer();
