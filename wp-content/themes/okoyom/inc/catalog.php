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

function okoyom_product_slides( int $product_id ): array {
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

	while ( count( $urls ) < 3 && $urls ) {
		$urls[] = $urls[0];
	}

	return array_slice( $urls, 0, 3 );
}

function okoyom_catalog_card( WP_Post $product ): void {
	$slides = okoyom_product_slides( $product->ID );
	if ( ! $slides ) {
		return;
	}

	$category = get_the_terms( $product->ID, 'product_cat' );
	$subject  = get_the_terms( $product->ID, 'oko_subject' );
	$price    = okoyom_product_base_price( $product->ID );
	?>
	<a href="<?php echo esc_url( get_permalink( $product ) ); ?>" class="blockCardCatalog__card" data-product-id="<?php echo esc_attr( (string) $product->ID ); ?>">
		<div class="hover-slider">
			<div class="likeCardCatalog" data-favorite="<?php echo esc_attr( (string) $product->ID ); ?>">
				<img src="<?php echo esc_url( OKOYOM_ASSETS_URI . '/img/like.svg' ); ?>" alt="В избранное" width="24" height="24" loading="lazy" decoding="async">
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
				<?php echo esc_html( $category && ! is_wp_error( $category ) ? $category[0]->name : '' ); ?>
			</p>
			<div class="flex-text-block-flexTwoTypeInfoMain">
				<span>
					<?php echo esc_html( $subject && ! is_wp_error( $subject ) ? mb_strtolower( $subject[0]->name ) : '' ); ?>
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

	$search   = (string) ( $GLOBALS['okoyom_search_query'] ?? '' );
	$products = okoyom_catalog_products( $scope, $search );

	if ( ! $products ) {

		echo '<p class="textTitleSection">' . ( $search
			? 'По запросу «' . esc_html( $search ) . '» ничего не найдено.'
			: 'Товары не найдены. Попробуйте сбросить фильтры.' ) . '</p>';

		return;
	}

	foreach ( $products as $product ) {
		okoyom_catalog_card( $product );
	}
}
