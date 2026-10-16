<?php

defined( 'ABSPATH' ) || exit;

get_header();

ob_start();
$okoyom_rendered = okoyom_static_part( 'catalog' );
$okoyom_html     = (string) ob_get_clean();

if ( $okoyom_rendered && function_exists( 'okoyom_apply_page_texts' ) ) {
	$okoyom_html = okoyom_apply_page_texts( $okoyom_html );
}

echo okoyom_catalog_apply_banner( $okoyom_html );

get_footer();
