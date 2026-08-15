<?php

defined( 'ABSPATH' ) || exit;

function okoyom_product_base_price( int $product_id ): float {
	$material_id = (int) get_post_meta( $product_id, '_okoyom_main_material', true );

	return $material_id ? (float) get_post_meta( $material_id, '_okoyom_price_per_sqm', true ) : 0.0;
}

function okoyom_catalog_products( string $scope = 'all', string $search = '' ): array {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);

	if ( 'all' !== $scope ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $scope,
			),
		);
	}

	if ( '' !== $search ) {
		global $wpdb;
		$like = '%' . $wpdb->esc_like( $search ) . '%';

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT p.ID FROM {$wpdb->posts} p
				 LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_sku'
				 WHERE p.post_type = 'product' AND p.post_status = 'publish'
				   AND (p.post_title LIKE %s OR m.meta_value LIKE %s)",
				$like,
				$like
			)
		);

		if ( ! $ids ) {
			return array();
		}
		$args['post__in'] = array_map( 'intval', $ids );
	}

	return get_posts( $args );
}

function okoyom_catalog_count(): string {
	$count = count( okoyom_catalog_products() );

	$last_two = $count % 100;
	$last     = $count % 10;

	if ( $last_two >= 11 && $last_two <= 14 ) {
		$word = 'работ';
	} elseif ( 1 === $last ) {
		$word = 'работа';
	} elseif ( $last >= 2 && $last <= 4 ) {
		$word = 'работы';
	} else {
		$word = 'работ';
	}

	return $count . ' ' . $word;
}

function okoyom_product_slides( int $product_id, int $limit = 3 ): array {
	$urls = array();

	$thumb = get_the_post_thumbnail_url( $product_id, 'large' );
	if ( $thumb ) {
		$urls[] = $thumb;
	}

	$gallery = get_post_meta( $product_id, '_product_image_gallery', true );
	foreach ( array_filter( explode( ',', (string) $gallery ) ) as $attachment_id ) {
		$url = wp_get_attachment_image_url( (int) $attachment_id, 'large' );
		if ( $url ) {
			$urls[] = $url;
		}
	}

	// limit 0 — все изображения (галерея карточки товара);
	// limit 3 — hover-слайдер в каталоге, добиваем обложкой до трёх кадров.
	if ( $limit > 0 ) {
		while ( count( $urls ) < $limit && $urls ) {
			$urls[] = $urls[0];
		}
		$urls = array_slice( $urls, 0, $limit );
	}

	return $urls;
}

function okoyom_catalog_card( WP_Post $product ): void {
	$slides = okoyom_product_slides( $product->ID );
	if ( ! $slides ) {
		return;
	}

	$collection = get_the_terms( $product->ID, 'oko_collection' );
	$price      = okoyom_product_base_price( $product->ID );
	?>
	<a href="<?php echo esc_url( get_permalink( $product ) ); ?>" class="blockCardCatalog__card" data-product-id="<?php echo esc_attr( (string) $product->ID ); ?>">
		<div class="hover-slider">
			<div class="likeCardCatalog" data-favorite="<?php echo esc_attr( (string) $product->ID ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="В избранное"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</div>
			<div class="hover-slider__slides">
				<?php foreach ( $slides as $i => $url ) : ?>
					<div class="hover-slider__slide<?php echo 0 === $i ? ' active' : ''; ?>">
						<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( get_the_title( $product ) ); ?>" loading="lazy" decoding="async">
					</div>
				<?php endforeach; ?>
			</div>
			<div class="hover-slider__zones">
				<?php foreach ( array_keys( $slides ) as $i ) : ?>
					<div data-index="<?php echo esc_attr( (string) $i ); ?>"></div>
				<?php endforeach; ?>
			</div>
			<div class="hover-slider__pagination">
				<?php foreach ( array_keys( $slides ) as $i ) : ?>
					<span<?php echo 0 === $i ? ' class="active"' : ''; ?>></span>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="text-block-flexTwoTypeInfoMain">
			<p>
				<?php echo esc_html( get_the_title( $product ) ); ?>
			</p>
			<div class="flex-text-block-flexTwoTypeInfoMain">
				<span>
					<?php echo esc_html( $collection && ! is_wp_error( $collection ) ? $collection[0]->name : '' ); ?>
				</span>
				<span>
					<?php echo esc_html( $price ? 'от ' . number_format( $price, 0, ',', ' ' ) . ' ₽/м²' : 'по запросу' ); ?>
				</span>
			</div>
		</div>
	</a>
	<?php
}

function okoyom_catalog_grid( string $scope = 'all' ): void {

	$search  = (string) ( $GLOBALS['okoyom_search_query'] ?? '' );
	$filters = function_exists( 'okoyom_active_filters' ) ? okoyom_active_filters() : array();

	if ( $filters && 'all' !== okoyom_current_scope() && okoyom_current_scope() !== $scope ) {
		$filters = array();
	}

	$products = $filters
		? okoyom_filtered_products( $scope, $filters, $search )
		: okoyom_catalog_products( $scope, $search );

	if ( ! $products ) {
		echo '<p class="textTitleSection">' . ( $search
			? 'По запросу «' . esc_html( $search ) . '» ничего не найдено.'
			: 'Ничего не найдено. Попробуйте сбросить фильтры.' ) . '</p>';

		return;
	}

	foreach ( $products as $product ) {
		okoyom_catalog_card( is_int( $product ) ? get_post( $product ) : $product );
	}
}
