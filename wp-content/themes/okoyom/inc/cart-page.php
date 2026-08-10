<?php

defined( 'ABSPATH' ) || exit;

function okoyom_render_cart_page(): string {
	ob_start();
	okoyom_static_part( 'bag-1' );
	$empty = (string) ob_get_clean();

	ob_start();
	okoyom_static_part( 'bag-2' );
	$full = (string) ob_get_clean();

	$empty = preg_replace(
		'/<section class="inspirationTop">/u',
		'<section class="inspirationTop" data-cart-empty>',
		$empty,
		1
	);

	$full = preg_replace(
		'/<section class="inspirationTop">/u',
		'<section class="inspirationTop" data-cart-full style="display:none">',
		$full,
		1
	);

	$opening = '<div class="card-left-flexBagPageContainer">';
	$cards   = array();
	$offset  = 0;
	while ( true ) {
		$start = strpos( $full, $opening, $offset );
		if ( false === $start ) {
			break;
		}

		$depth = 0;
		$pos   = $start;
		$len   = strlen( $full );
		while ( $pos < $len ) {
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
				$pos = $close + 6;
				if ( 0 === $depth ) {
					break;
				}
			}
		}
		$cards[] = array( $start, $pos );
		$offset  = $pos;
	}

	if ( $cards ) {

		for ( $i = count( $cards ) - 1; $i >= 1; $i-- ) {
			$full = substr_replace( $full, '', $cards[ $i ][0], $cards[ $i ][1] - $cards[ $i ][0] );
		}

		list( $t_start, $t_end ) = $cards[0];
		$template = substr( $full, $t_start, $t_end - $t_start );

		$template = str_replace(
			'<div class="card-left-flexBagPageContainer">',
			'<div class="card-left-flexBagPageContainer" data-cart-item-template style="display:none">',
			$template
		);
		$template = preg_replace(
			'/(<p class="title-text-left-card-left-flexBagPageContainer">)\s*[^<]*/u',
			'$1<span data-cart-field="title"></span>',
			$template,
			1
		);
		$template = preg_replace(
			'/(<p class="text-text-left-card-left-flexBagPageContainer">).*?(<\/p>)/su',
			'$1<span data-cart-field="size"></span><br><span data-cart-field="area"></span><br><span data-cart-field="material"></span>$2',
			$template,
			1
		);
		$template = preg_replace(
			'/(<p class="price-card-left-flexBagPageContainer">)\s*[^<]*/u',
			'$1<span data-cart-field="price"></span>',
			$template,
			1
		);
		$template = preg_replace(
			'/<a href="[^"]*">(\s*<img[^>]*close\.svg[^>]*>\s*Убрать из корзины\s*)<\/a>/u',
			'<a href="#" data-cart-remove>$1</a>',
			$template,
			1
		);

		$full = substr_replace( $full, $template, $t_start, $t_end - $t_start );

		$full = str_replace(
			'<div class="left-flexBagPageContainer">',
			'<div class="left-flexBagPageContainer" data-cart-items>',
			$full
		);
	}

	$full = preg_replace(
		'/<a href="[^"]*" class="btnWhiteTextBtn btnWhiteTextBtnV3">(\s*Оформить заказ)/u',
		'<a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV3 openModal" data-lead-type="cart_request">$1',
		$full,
		1
	);

	$full = preg_replace( '/(<a href="[^"]*" class="btn-left-flexBagPageContainer">)/u', '<a href="#" class="btn-left-flexBagPageContainer" data-cart-clear>', $full, 1 );
	$full = preg_replace( '/Товары \(\d+\)/u', '<span data-cart-count>Товары (0)</span>', $full, 1 );
	$full = preg_replace( '/(<span style="color: rgba\(22, 20, 18, 0\.65\);">)\s*35 100 ₽\s*(<\/span>)/u', '$1<span data-cart-total></span>$2', $full, 1 );
	$full = preg_replace( '/<h2>\s*35 100 ₽\s*<\/h2>/u', '<h2 data-cart-total></h2>', $full, 1 );

	return $empty . $full;
}
