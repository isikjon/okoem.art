<?php

defined( 'ABSPATH' ) || exit;

function okoyom_render_favorites_page(): string {
	ob_start();
	okoyom_static_part( 'like-1' );
	$empty = (string) ob_get_clean();

	$empty = preg_replace(
		'/<section class="inspirationTop">/u',
		'<section class="inspirationTop" data-fav-empty>',
		$empty,
		1
	);

	ob_start();
	okoyom_static_part( 'like-2' );
	$full = (string) ob_get_clean();

	$full = preg_replace(
		'/<section class="inspirationTop">/u',
		'<section class="inspirationTop" data-fav-full style="display:none">',
		$full,
		1
	);

	$opening = '<div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2">';
	$start   = strpos( $full, $opening );
	if ( false !== $start ) {
		$inner_start = $start + strlen( $opening );
		$depth       = 1;
		$pos         = $inner_start;
		$len         = strlen( $full );
		while ( $pos < $len && $depth > 0 ) {
			$open  = strpos( $full, '<div', $pos );
			$close = strpos( $full, '</div>', $pos );
			if ( false === $close ) {
				break;
			}
			if ( false !== $open && $open < $close ) {
				++$depth;
				$pos = $open + 4;
			} else {
				--$depth;
				if ( 0 === $depth ) {
					break;
				}
				$pos = $close + 6;
			}
		}

		ob_start();
		okoyom_catalog_grid( 'all' );
		$cards = (string) ob_get_clean();

		$full = substr_replace( $full, $cards, $inner_start, $close - $inner_start );
		$full = str_replace( $opening, '<div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2" data-fav-grid>', $full );
	}

	return $empty . $full;
}
