<?php

defined( 'ABSPATH' ) || exit;

function okoyom_product_materials( int $product_id ): array {
	$main  = (int) get_post_meta( $product_id, '_okoyom_main_material', true );
	$extra = get_post_meta( $product_id, '_okoyom_extra_materials', true );
	$ids   = array_merge( $main ? array( $main ) : array(), is_array( $extra ) ? array_map( 'intval', $extra ) : array() );

	$materials = array();
	foreach ( array_unique( $ids ) as $id ) {
		$post = get_post( $id );
		if ( ! $post || 'oko_material' !== $post->post_type ) {
			continue;
		}
		$materials[] = array(
			'id'    => $id,
			'name'  => $post->post_title,
			'price' => (float) get_post_meta( $id, '_okoyom_price_per_sqm', true ),
			'seam'  => (string) get_post_meta( $id, '_okoyom_seam_type', true ),
			'strip' => (int) get_post_meta( $id, '_okoyom_strip_width', true ),
		);
	}

	return $materials;
}

function okoyom_initial_calc( float $price_per_sqm, int $w = 300, int $h = 300 ): array {
	$area  = ( ( $w + 5 ) / 100 ) * ( ( $h + 5 ) / 100 );
	$total = round( $area * $price_per_sqm );

	return array(
		'area'  => number_format( $area, 2, '.', '' ),
		'total' => number_format( $total, 0, ',', ' ' ),
	);
}

function okoyom_render_product_page( WP_Post $product ): string {
	ob_start();
	okoyom_static_part( 'card' );
	$html = (string) ob_get_clean();

	$title      = get_the_title( $product );
	$excerpt    = get_the_excerpt( $product );
	$collection = get_the_terms( $product->ID, 'oko_collection' );
	$collection = $collection && ! is_wp_error( $collection ) ? $collection[0]->name : '';
	$materials  = okoyom_product_materials( $product->ID );
	$main       = $materials ? $materials[0] : null;
	$slides     = okoyom_product_slides( $product->ID, 0 );

	$html = preg_replace_callback(
		'/(<div class="title-right-flex-cardSectionContent">)(.*?)(<\/div>)/su',
		function ( array $m ) use ( $title, $collection, $excerpt ) {
			$block = $m[2];
			if ( '' !== $collection ) {
				$block = preg_replace( '/(<span>)\s*(?:<\?php[^?]*\?>|[^<]*)\s*(<\/span>)/u', '$1' . esc_html( $collection ) . '$2', $block, 1 );
			}
			$block = preg_replace( '/(<h1>)\s*(?:<\?php[^?]*\?>|[^<]*)\s*(<\/h1>)/u', '$1' . esc_html( $title ) . '$2', $block, 1 );
			if ( '' !== $excerpt ) {
				$block = preg_replace( '/(<p>)\s*(?:<\?php[^?]*\?>|[^<]*)\s*(<\/p>)/u', '$1' . esc_html( $excerpt ) . '$2', $block, 1 );
			}
			return $m[1] . $block . $m[3];
		},
		$html,
		1
	);

	$html = str_replace( 'Дальние хребты', esc_html( $title ), $html );

	$input   = '<input type="text" placeholder="300" value="300">';
	$pos     = strpos( $html, $input );
	if ( false !== $pos ) {
		$html = substr_replace( $html, '<input type="text" inputmode="numeric" placeholder="300" value="300" data-calc="w">', $pos, strlen( $input ) );
	}
	$pos = strpos( $html, $input );
	if ( false !== $pos ) {
		$html = substr_replace( $html, '<input type="text" inputmode="numeric" placeholder="300" value="300" data-calc="h">', $pos, strlen( $input ) );
	}

	$price_re = '/<h2>[\s]*35[\s\x{00A0}]+100[\s\x{00A0}]*₽\s*<\/h2>/u';
	if ( $main ) {
		$calc = okoyom_initial_calc( $main['price'] );
		$html = preg_replace( '/<p>\s*7\.80 м²\s*<\/p>/u', '<p data-calc="area">' . esc_html( $calc['area'] ) . ' м²</p>', $html, 1 );
		$html = preg_replace( $price_re, '<h2 data-calc="price">' . esc_html( $calc['total'] ) . ' ₽</h2>', $html, 1 );
	} else {

		$html = preg_replace( '/<p>\s*7\.80 м²\s*<\/p>/u', '<p>—</p>', $html, 1 );
		$html = preg_replace( $price_re, '<h2>По запросу</h2>', $html, 1 );
		$html = preg_replace( '/(<span style="color: rgba\(22, 20, 18, 0\.65\);">)\s*7\.80 м²\s*(<\/span>)/u', '$1—$2', $html, 1 );
		$html = preg_replace( $price_re, '<h2>По запросу</h2>', $html, 1 );
	}

	if ( $main ) {
		$html = preg_replace(
			'/(<span class="material-select__value">)\s*[^<]*(<\/span>)/u',
			'$1' . esc_html( $main['name'] ) . '$2',
			$html,
			1
		);

		$items = '';
		foreach ( $materials as $i => $material ) {
			$items .= sprintf(
				'<button class="material-select__item%s" type="button" data-material="%d"><div class="material-select__left"><span class="material-select__dot"></span><span class="material-select__name">%s</span></div><span class="material-select__price">%s ₽/м²</span></button>',
				0 === $i ? ' is-active' : '',
				$material['id'],
				esc_html( $material['name'] ),
				esc_html( number_format( $material['price'], 0, ',', ' ' ) )
			);
		}
		$html = preg_replace(
			'/(<div class="material-select__list">).*?(<\/div>\s*<\/div>\s*<\/div>)/su',
			'$1' . $items . '$2',
			$html,
			1
		);
	}

	if ( $main ) {

		$html = str_replace(
			'<input disabled type="text" placeholder="300" value="Основа">',
			'<input disabled type="text" data-calc-bg="material-input" value="' . esc_attr( $main['name'] ) . '">',
			$html
		);

		$input = '<input type="text" placeholder="300" value="300">';
		$pos   = strpos( $html, $input );
		if ( false !== $pos ) {
			$html = substr_replace( $html, '<input type="text" inputmode="numeric" placeholder="300" value="300" data-calc-bg="w">', $pos, strlen( $input ) );
		}
		$pos = strpos( $html, $input );
		if ( false !== $pos ) {
			$html = substr_replace( $html, '<input type="text" inputmode="numeric" placeholder="300" value="300" data-calc-bg="h">', $pos, strlen( $input ) );
		}

		$calc = okoyom_initial_calc( $main['price'] );
		$html = preg_replace(
			'/(<span style="color: rgba\(22, 20, 18, 0\.65\);">)\s*7\.80 м²\s*(<\/span>)/u',
			'$1<span data-calc-bg="area">' . esc_html( $calc['area'] ) . ' м²</span>$2',
			$html,
			1
		);
		$html = preg_replace(
			'/(<span style="color: rgba\(22, 20, 18, 0\.65\);">)\s*Основа\s*(<\/span>)/u',
			'$1<span data-calc-bg="material">' . esc_html( $main['name'] ) . '</span>$2',
			$html,
			1
		);
		$html = preg_replace(
			$price_re,
			'<h2 data-calc-bg="price">' . esc_html( $calc['total'] ) . ' ₽</h2>',
			$html,
			1
		);

		$html = preg_replace(
			'/<a href="[^"]*" class="btnWhiteTextBtn btnWhiteTextBtnV3"[^>]*>(\s*Отправить запрос)/u',
			'<a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV3 openModal" data-lead-type="companion_request">$1',
			$html,
			1
		);
	}

	$html = str_replace(
		'<a href="/favorites/" class="flexBtnsCards__links">',
		'<a href="' . esc_url( okoyom_favorites_url() ) . '" class="flexBtnsCards__links" data-favorite="' . esc_attr( (string) $product->ID ) . '">',
		$html
	);

	$html = preg_replace(
		'/<a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV3">(\s*Запросить расчёт)/u',
		'<a href="' . esc_url( okoyom_cart_url() ) . '" class="btnWhiteTextBtn btnWhiteTextBtnV3" data-add-to-cart>$1',
		$html,
		1
	);

	if ( $slides ) {
		$make_slides = static function () use ( $slides, $product ): string {
			$out = '';
			foreach ( $slides as $url ) {
				$out .= sprintf(
					'<div class="swiper-slide"><img src="%s" alt="%s" loading="lazy" decoding="async"></div>',
					esc_url( $url ),
					esc_attr( get_the_title( $product ) )
				);
			}
			return $out;
		};

		foreach ( array( 'muralGalleryThumbs', 'muralGalleryMain' ) as $gallery_class ) {
			$anchor = '<div class="swiper ' . $gallery_class;
			$start  = strpos( $html, $anchor );
			if ( false === $start ) {
				continue;
			}
			$wrap_open = strpos( $html, '<div class="swiper-wrapper">', $start );
			if ( false === $wrap_open ) {
				continue;
			}
			$inner_start = $wrap_open + strlen( '<div class="swiper-wrapper">' );
			$depth       = 1;
			$pos         = $inner_start;
			$len         = strlen( $html );
			while ( $pos < $len && $depth > 0 ) {
				$open  = strpos( $html, '<div', $pos );
				$close = strpos( $html, '</div>', $pos );
				if ( false === $close ) {
					break;
				}
				if ( false !== $open && $open < $close ) {
					++$depth;
					$pos = $open + 4;
				} else {
					--$depth;
					if ( 0 === $depth ) {
						$html = substr( $html, 0, $inner_start ) . $make_slides() . substr( $html, $close );
						break;
					}
					$pos = $close + 6;
				}
			}
		}
	}

	$versions = function_exists( 'okoyom_color_versions' ) ? okoyom_color_versions( $product->ID ) : array();
	if ( count( $versions ) > 1 ) {
		$dots = '';
		foreach ( $versions as $i => $v ) {
			$dots .= sprintf(
				'<div class="block-flexColorsCards%s" style="background: %s;" data-color-version="%d" data-color-image="%s" data-color-title="%s"></div>',
				0 === $i ? ' block-flexColorsCards__active' : '',
				esc_attr( $v['hex'] ),
				$i,
				esc_url( $v['image'] ),
				esc_attr( $v['title'] )
			);
		}
		$html = preg_replace(
			'/(<div class="flex-flexColorsCards">).*?(<\/div>)(\s*<p>)/su',
			'$1' . $dots . '$2$3',
			$html,
			1
		);
		$html = preg_replace(
			'/(<div class="flexColorsCards">.*?<\/div>\s*<p>)\s*[^<]*(<\/p>)/su',
			'$1' . esc_html( $versions[0]['title'] ) . '$2',
			$html,
			1
		);
	} else {
		$html = okoyom_remove_block( $html, '<div class="flexColorsCards">' );
	}

	return $html;
}

function okoyom_remove_block( string $html, string $opening ): string {
	$start = strpos( $html, $opening );
	if ( false === $start ) {
		return $html;
	}
	$depth = 0;
	$pos   = $start;
	$len   = strlen( $html );
	while ( $pos < $len ) {
		$open  = strpos( $html, '<div', $pos );
		$close = strpos( $html, '</div>', $pos );
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
				return substr( $html, 0, $start ) . substr( $html, $pos );
			}
		}
	}
	return $html;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		$product   = get_queried_object();
		$materials = okoyom_product_materials( $product->ID );

		wp_localize_script(
			'okoyom-theme',
			'okoyomProduct',
			array(
				'id'        => $product->ID,
				'title'     => get_the_title( $product ),
				'sku'       => (string) get_post_meta( $product->ID, '_sku', true ),
				'url'       => get_permalink( $product ),
				'image'     => get_the_post_thumbnail_url( $product, 'medium' ),
				'materials' => $materials,
				'limits'    => array(
					'wMin' => 1,
					'wMax' => 10000,
					'hMin' => 1,
					'hMax' => 6000,
				),
			)
		);
	},
	20
);
