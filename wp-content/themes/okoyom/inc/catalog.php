<?php

defined( 'ABSPATH' ) || exit;

function okoyom_product_base_price( int $product_id ): float {
	$material_id = (int) get_post_meta( $product_id, '_okoyom_main_material', true );

	return $material_id ? (float) get_post_meta( $material_id, '_okoyom_price_per_sqm', true ) : 0.0;
}

function okoyom_archive_tax_term(): ?array {
	foreach ( array( 'oko_collection', 'oko_series', 'oko_subject', 'oko_color', 'product_cat' ) as $tax ) {
		if ( is_tax( $tax ) ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				return array( 'taxonomy' => $tax, 'slug' => $term->slug, 'name' => $term->name );
			}
		}
	}
	return null;
}

function okoyom_catalog_per_page(): int {
	$per_page = (int) apply_filters( 'okoyom_catalog_per_page', 24 );

	return $per_page > 0 ? $per_page : 24;
}

function okoyom_catalog_paged(): int {
	$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

	return $paged > 0 ? $paged : 1;
}

function okoyom_request_value( string $key ): string {
	if ( ! isset( $_GET[ $key ] ) || ! is_scalar( $_GET[ $key ] ) ) {
		return '';
	}

	return (string) wp_unslash( $_GET[ $key ] );
}

function okoyom_search_normalize( string $text ): string {
	return trim( (string) preg_replace( '~\s+~u', ' ', str_replace( array( '–', '—', '−', '‑', '‐' ), '-', $text ) ) );
}

function okoyom_catalog_search(): string {
	$search = (string) ( $GLOBALS['okoyom_search_query'] ?? '' );
	if ( '' === $search ) {
		$search = okoyom_request_value( 'q' );
	}

	return sanitize_text_field( $search );
}

function okoyom_catalog_sort(): string {
	return 'new' === sanitize_key( okoyom_request_value( 'sort' ) ) ? 'new' : 'default';
}

function okoyom_catalog_ids( string $scope = 'all', string $search = '' ): array {
	static $cache = array();
	$key = $scope . '|' . $search . '|' . okoyom_catalog_sort() . '|' . wp_json_encode( okoyom_active_filters() );

	if ( ! isset( $cache[ $key ] ) ) {
		$cache[ $key ] = okoyom_catalog_query_ids( $scope, $search );
	}

	return $cache[ $key ];
}

function okoyom_catalog_found( string $scope = 'all', string $search = '' ): int {
	return count( okoyom_catalog_ids( $scope, $search ) );
}

function okoyom_catalog_max_pages( string $scope = 'all', string $search = '' ): int {
	return (int) max( 1, (int) ceil( okoyom_catalog_found( $scope, $search ) / okoyom_catalog_per_page() ) );
}

function okoyom_catalog_query_ids( string $scope = 'all', string $search = '' ): array {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'new' === okoyom_catalog_sort()
			? array( 'date' => 'DESC', 'ID' => 'DESC' )
			: array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'exclude-from-catalog' ),
				'operator' => 'NOT IN',
			),
		),
	);

	if ( 'all' !== $scope ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $scope,
		);
	}

	$archive = okoyom_archive_tax_term();
	if ( $archive ) {
		$args['tax_query'][] = array(
			'taxonomy' => $archive['taxonomy'],
			'field'    => 'slug',
			'terms'    => $archive['slug'],
		);
	}

	foreach ( okoyom_active_filters() as $param => $slugs ) {
		$taxonomy = okoyom_filter_taxonomies_map()[ $param ][0] ?? '';
		if ( '' === $taxonomy ) {
			continue;
		}

		$args['tax_query'][] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => okoyom_filter_query_slugs( $param, $slugs ),
			'operator' => 'IN',
		);
	}

	if ( '' !== $search ) {
		global $wpdb;
		$normalized = okoyom_search_normalize( $search );
		$like       = '%' . $wpdb->esc_like( $normalized ) . '%';
		$tight      = '%' . $wpdb->esc_like( str_replace( ' ', '', $normalized ) ) . '%';

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT p.ID FROM {$wpdb->posts} p
				 LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_sku'
				 WHERE p.post_type = 'product' AND p.post_status = 'publish'
				   AND (REPLACE( REPLACE( p.post_title, '–', '-' ), '—', '-' ) LIKE %s
				        OR REPLACE( REPLACE( REPLACE( p.post_title, '–', '-' ), '—', '-' ), ' ', '' ) LIKE %s
				        OR m.meta_value LIKE %s
				        OR REPLACE( m.meta_value, ' ', '' ) LIKE %s)",
				$like,
				$tight,
				$like,
				$tight
			)
		);

		if ( ! $ids ) {
			return array();
		}
		$args['post__in'] = array_map( 'intval', $ids );
	}

	return array_map( 'intval', get_posts( $args ) );
}

function okoyom_catalog_products( string $scope = 'all', string $search = '', bool $paginate = false ): array {
	$ids = okoyom_catalog_ids( $scope, $search );
	if ( ! $ids ) {
		return array();
	}

	if ( $paginate ) {
		$per_page = okoyom_catalog_per_page();
		$ids      = array_slice( $ids, ( okoyom_catalog_paged() - 1 ) * $per_page, $per_page );
		if ( ! $ids ) {
			return array();
		}
	}

	_prime_post_caches( $ids, true, true );

	return array_map( 'get_post', $ids );
}

function okoyom_catalog_count(): string {
	$count = okoyom_catalog_found( 'all', okoyom_catalog_search() );

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

function okoyom_attachment_alt( int $attachment_id, string $fallback ): string {
	$alt = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

	return '' !== $alt ? $alt : $fallback;
}

function okoyom_product_slide_items( int $product_id, bool $with_thumb = true ): array {
	$fallback = okoyom_crumb_text( get_the_title( $product_id ) );
	$items    = array();
	$ids      = array();

	$thumb = (int) get_post_thumbnail_id( $product_id );
	if ( $with_thumb && $thumb ) {
		$ids[] = $thumb;
	}

	foreach ( array_filter( explode( ',', (string) get_post_meta( $product_id, '_product_image_gallery', true ) ) ) as $attachment_id ) {
		$ids[] = (int) $attachment_id;
	}

	if ( ! $ids && $thumb ) {
		$ids[] = $thumb;
	}

	foreach ( array_unique( $ids ) as $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( ! $url ) {
			continue;
		}

		$items[] = array(
			'id'  => $attachment_id,
			'src' => (string) $url,
			'alt' => okoyom_attachment_alt( $attachment_id, $fallback ),
		);
	}

	return $items;
}

function okoyom_product_slides( int $product_id, int $limit = 3, bool $with_thumb = true ): array {
	$urls = array();

	$thumb = get_the_post_thumbnail_url( $product_id, 'large' );
	if ( $with_thumb && $thumb ) {
		$urls[] = $thumb;
	}

	$gallery = get_post_meta( $product_id, '_product_image_gallery', true );
	foreach ( array_filter( explode( ',', (string) $gallery ) ) as $attachment_id ) {
		$url = wp_get_attachment_image_url( (int) $attachment_id, 'large' );
		if ( $url ) {
			$urls[] = $url;
		}
	}

	if ( ! $urls && $thumb ) {
		$urls[] = $thumb;
	}

	if ( $limit > 0 ) {
		while ( count( $urls ) < $limit && $urls ) {
			$urls[] = $urls[0];
		}
		$urls = array_slice( $urls, 0, $limit );
	}

	return $urls;
}

function okoyom_room_tokens_map(): array {
	return apply_filters(
		'okoyom_room_tokens_map',
		array(
			'v-spalnyu'                => array( 'spalni' ),
			'v-gostinuyu'              => array( 'gostinoy', 'kukhni-gostinoy' ),
			'na-kukhnyu-i-v-stolovuyu' => array( 'stolovoy', 'kukhni-gostinoy', 'kukhni-stolovoy', 'kukhni' ),
			'dlya-restoranov-i-oteley' => array(
				'restorana',
				'kafe',
				'bara',
				'spa',
				'launzh-restorana',
				'launzh-zony',
				'launzh-bara',
				'resepshena',
				'lobbi',
				'launzh-otelya',
				'koridora-otelya',
			),
		)
	);
}

function okoyom_current_room_slug(): string {
	if ( ! is_tax( 'product_cat' ) ) {
		return '';
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || ! isset( okoyom_room_tokens_map()[ $term->slug ] ) ) {
		return '';
	}

	return $term->slug;
}

function okoyom_current_room_tokens(): array {
	$slug = okoyom_current_room_slug();

	return '' !== $slug ? okoyom_room_tokens_map()[ $slug ] : array();
}

function okoyom_request_room_tokens(): array {
	$value = isset( $_GET['room'] ) ? sanitize_title( wp_unslash( (string) $_GET['room'] ) ) : '';
	$slug  = '' !== $value ? okoyom_room_value_to_slug( $value ) : '';

	return '' !== $slug ? ( okoyom_room_tokens_map()[ $slug ] ?? array() ) : array();
}

function okoyom_room_image_id( int $product_id, array $tokens ): int {
	if ( ! $tokens ) {
		return 0;
	}

	$ids   = array_filter( explode( ',', (string) get_post_meta( $product_id, '_product_image_gallery', true ) ) );
	$thumb = (int) get_post_thumbnail_id( $product_id );
	if ( $thumb ) {
		$ids[] = $thumb;
	}

	if ( ! $ids ) {
		return 0;
	}

	$names = array();
	foreach ( array_unique( array_map( 'intval', $ids ) ) as $attachment_id ) {
		$file = get_attached_file( $attachment_id );
		if ( $file ) {
			$names[ $attachment_id ] = basename( $file );
		}
	}

	foreach ( $tokens as $token ) {
		$pattern = '~-v-interere-' . preg_quote( $token, '~' ) . '(-\d+)*(-scaled)?\.[a-z0-9]+$~i';
		foreach ( $names as $attachment_id => $name ) {
			if ( preg_match( $pattern, $name ) ) {
				return $attachment_id;
			}
		}
	}

	return 0;
}

function okoyom_room_image_map( int $product_id ): array {
	$map = array();
	foreach ( okoyom_room_tokens_map() as $slug => $tokens ) {
		$value = okoyom_room_slug_to_value( $slug );
		if ( '' === $value ) {
			continue;
		}

		$id = okoyom_room_image_id( $product_id, $tokens );
		if ( ! $id ) {
			continue;
		}

		$url = (string) wp_get_attachment_image_url( $id, 'large' );
		if ( '' === $url ) {
			continue;
		}

		$alt         = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		$map[ $value ] = array( 'src' => $url, 'alt' => $alt );
	}

	return $map;
}

function okoyom_home_main_heading( string $html ): string {
	return preg_replace(
		'~<h2(\s+class="titleAllForSection[^"]*")>(.*?)</h2>~su',
		'<h1$1>$2</h1>',
		$html,
		1
	);
}

function okoyom_catalog_heading(): array {
	$term = is_tax( 'product_cat' ) ? get_queried_object() : null;

	if ( $term instanceof WP_Term && isset( okoyom_room_tokens_map()[ $term->slug ] ) ) {
		$h1 = trim( (string) get_term_meta( $term->term_id, 'okoyom_h1', true ) );

		return array(
			'big'  => $term->name,
			'h1'   => '' !== $h1 ? $h1 : $term->name,
			'text' => trim( (string) term_description( $term->term_id, 'product_cat' ) ),
		);
	}

	$selection = is_tax( array( 'oko_collection', 'oko_series', 'oko_subject', 'oko_color' ) ) ? get_queried_object() : null;

	if ( $selection instanceof WP_Term ) {
		$own = trim( (string) get_term_meta( $selection->term_id, 'okoyom_h1', true ) );

		return array(
			'big'  => $selection->name,
			'h1'   => '' !== $own ? $own : okoyom_selection_heading( $selection ),
			'text' => trim( (string) term_description( $selection->term_id, $selection->taxonomy ) ),
		);
	}

	$text = '';
	if ( ! okoyom_active_filters() ) {
		$page = get_page_by_path( 'catalog' );
		$text = $page ? trim( apply_filters( 'the_content', $page->post_content ) ) : '';
	}

	return array(
		'big'  => 'Каталог',
		'h1'   => 'Интерьерные муралы — фрески на стену',
		'text' => $text,
	);
}

function okoyom_selection_heading( WP_Term $term ): string {
	$patterns = array(
		'oko_collection' => 'Интерьерные муралы — коллекция «%s»',
		'oko_series'     => 'Интерьерные муралы — серия «%s»',
		'oko_subject'    => 'Интерьерные муралы — %s',
		'oko_color'      => 'Интерьерные муралы — %s',
	);

	$pattern = $patterns[ $term->taxonomy ] ?? 'Интерьерные муралы — %s';
	$name    = $term->name;

	if ( 'oko_subject' === $term->taxonomy || 'oko_color' === $term->taxonomy ) {
		$name = function_exists( 'mb_strtolower' ) ? mb_strtolower( $name, 'UTF-8' ) : strtolower( $name );
	}

	return sprintf( $pattern, $name );
}

function okoyom_catalog_apply_banner( string $html ): string {
	$heading = okoyom_catalog_heading();

	$html = preg_replace_callback(
		'~(<div class="titleCatalog">\s*)<h1(\s+class="titleCatalog__title[^"]*")>.*?</h1>(\s*)<p(\s+class="titleCatalog__text[^"]*")>.*?</p>~su',
		static function ( array $m ) use ( $heading ): string {
			return $m[1] . '<div' . $m[2] . '>' . esc_html( $heading['big'] ) . '</div>'
				. $m[3] . '<h1' . $m[4] . '>' . esc_html( $heading['h1'] ) . '</h1>';
		},
		$html,
		1
	);

	$html .= '<section class="sectionMain catalogBottomText">'
		. ( '' !== $heading['text']
			? '<div class="container"><div class="staticContent">' . $heading['text'] . '</div></div>'
			: '' )
		. '</section>';

	if ( function_exists( 'okoyom_filter_links_nav' ) ) {
		ob_start();
		okoyom_filter_links_nav();
		$html .= (string) ob_get_clean();
	}

	return $html;
}

function okoyom_product_series_name( int $product_id ): string {
	$terms = get_the_terms( $product_id, 'oko_series' );

	return $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
}

function okoyom_card_room_value(): string {
	$rooms = okoyom_active_filters()['room'] ?? array();

	return 1 === count( $rooms ) ? (string) reset( $rooms ) : '';
}

function okoyom_card_room_tokens(): array {
	$slug = okoyom_room_value_to_slug( okoyom_card_room_value() );

	return '' !== $slug ? ( okoyom_room_tokens_map()[ $slug ] ?? array() ) : array();
}

function okoyom_catalog_card( WP_Post $product, int $order = -1 ): void {
	$slides = okoyom_product_slides( $product->ID, 3, false );
	if ( ! $slides ) {
		return;
	}

	$title    = okoyom_crumb_text( get_the_title( $product ) );
	$by_url   = array();
	foreach ( okoyom_product_slide_items( $product->ID, false ) as $item ) {
		$by_url[ $item['src'] ] = $item['alt'];
	}

	$alts = array();
	$base = array();
	foreach ( $slides as $base_url ) {
		$alt    = $by_url[ $base_url ] ?? $title;
		$alts[] = $alt;
		$base[] = array( 'src' => $base_url, 'alt' => $alt );
	}
	$room_id  = okoyom_room_image_id( $product->ID, okoyom_card_room_tokens() );
	$room_url = $room_id ? (string) wp_get_attachment_image_url( $room_id, 'large' ) : '';

	if ( '' !== $room_url ) {
		$slides = array_slice( array_values( array_unique( array_merge( array( $room_url ), $slides ) ) ), 0, 3 );
		$alts   = array();
		foreach ( $slides as $slide_url ) {
			$alts[] = $slide_url === $room_url
				? okoyom_attachment_alt( $room_id, $title )
				: ( $by_url[ $slide_url ] ?? $title );
		}
	}

	$series   = okoyom_product_series_name( $product->ID );
	$room     = okoyom_card_room_value();
	$card_url = get_permalink( $product );
	if ( '' !== $room ) {
		$card_url = add_query_arg( 'room', $room, $card_url );
	}

	$room_map   = okoyom_room_image_map( $product->ID );
	$card_rooms = array_values(
		array_filter(
			array_map(
				'okoyom_room_slug_to_value',
				wp_list_pluck( (array) get_the_terms( $product->ID, 'product_cat' ), 'slug' )
			)
		)
	);

	$slug_csv = static function ( int $pid, string $tax ): string {
		$terms = get_the_terms( $pid, $tax );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return '';
		}
		return implode( ',', wp_list_pluck( $terms, 'slug' ) );
	};
	?>
	<a href="<?php echo esc_url( $card_url ); ?>" class="blockCardCatalog__card"
		data-product-id="<?php echo esc_attr( (string) $product->ID ); ?>"
		data-collection="<?php echo esc_attr( $slug_csv( $product->ID, 'oko_collection' ) ); ?>"
		data-series="<?php echo esc_attr( $slug_csv( $product->ID, 'oko_series' ) ); ?>"
		data-subject="<?php echo esc_attr( $slug_csv( $product->ID, 'oko_subject' ) ); ?>"
		data-color="<?php echo esc_attr( $slug_csv( $product->ID, 'oko_color' ) ); ?>"
		data-room="<?php echo esc_attr( implode( ',', $card_rooms ) ); ?>"
		data-rooms="<?php echo esc_attr( (string) wp_json_encode( $room_map ) ); ?>"
		data-slides="<?php echo esc_attr( (string) wp_json_encode( $base ) ); ?>"
		data-sku="<?php echo esc_attr( (string) get_post_meta( $product->ID, '_sku', true ) ); ?>"
		data-date="<?php echo esc_attr( (string) get_post_time( 'U', true, $product ) ); ?>"
		<?php echo $order >= 0 ? 'data-order="' . esc_attr( (string) $order ) . '"' : ''; ?>
		data-title="<?php echo esc_attr( mb_strtolower( get_the_title( $product ) ) ); ?>">
		<div class="hover-slider">
			<div class="likeCardCatalog" data-favorite="<?php echo esc_attr( (string) $product->ID ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="В избранное"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</div>
			<div class="hover-slider__slides">
				<?php foreach ( $slides as $i => $url ) : ?>
					<div class="hover-slider__slide<?php echo 0 === $i ? ' active' : ''; ?>">
						<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alts[ $i ] ?? $title ); ?>" loading="lazy" decoding="async">
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
					<?php echo esc_html( $series ); ?>
				</span>
			</div>
		</div>
	</a>
	<?php
}

function okoyom_catalog_is_paginated(): bool {
	return ! is_page( 'search' ) && ! is_page( 'favorites' );
}

function okoyom_catalog_grid( string $scope = 'all', bool $paginate = false ): void {

	$search   = okoyom_catalog_search();
	$paginate = $paginate && okoyom_catalog_is_paginated();

	$products = okoyom_catalog_products( $scope, $search, $paginate );

	if ( ! $products ) {
		echo '<p class="textTitleSection">' . ( $search
			? 'По запросу «' . esc_html( $search ) . '» ничего не найдено.'
			: 'Ничего не найдено.' ) . '</p>';

		return;
	}

	$offset = $paginate ? ( okoyom_catalog_paged() - 1 ) * okoyom_catalog_per_page() : 0;

	foreach ( array_values( $products ) as $i => $product ) {
		okoyom_catalog_card( is_int( $product ) ? get_post( $product ) : $product, $offset + $i );
	}
}

function okoyom_catalog_page_url( int $page, bool $own_only = false ): string {
	$base = okoyom_catalog_base_url();

	if ( $page > 1 ) {
		$base = trailingslashit( $base ) . 'page/' . $page . '/';
	}

	$query = array();
	parse_str( (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_QUERY ), $query );
	unset( $query['paged'], $query['page'] );

	if ( $own_only ) {
		$keep  = array_merge( array_keys( okoyom_filter_taxonomies_map() ), array( 'q', 'sort', 'tab' ) );
		$query = array_intersect_key( $query, array_flip( $keep ) );
		$query = array_filter(
			$query,
			static function ( $value ) {
				return is_scalar( $value ) && '' !== (string) $value;
			}
		);
	}

	return $query ? $base . '?' . http_build_query( $query ) : $base;
}

function okoyom_catalog_base_url(): string {
	$path = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH );
	$path = (string) preg_replace( '~/page/\d+/?$~', '/', $path );

	return '' !== $path ? user_trailingslashit( $path ) : '/catalog/';
}

function okoyom_catalog_pager( string $scope = 'all' ): void {
	if ( ! okoyom_catalog_is_paginated() ) {
		return;
	}

	$search = okoyom_catalog_search();
	$max    = okoyom_catalog_max_pages( $scope, $search );
	if ( $max < 2 ) {
		return;
	}

	$paged = min( okoyom_catalog_paged(), $max );
	?>
	<div class="btnsFlexCenter btnsFlexCenter-22 catalogPager" data-pager data-page="<?php echo esc_attr( (string) $paged ); ?>" data-loaded="<?php echo esc_attr( (string) $paged ); ?>" data-max="<?php echo esc_attr( (string) $max ); ?>">
		<?php if ( $paged < $max ) : ?>
			<a href="<?php echo esc_url( okoyom_catalog_page_url( $paged + 1 ) ); ?>" class="btnWhiteTextBtn btnWhiteTextBtnV2" data-pager-more rel="next">Смотреть ещё</a>
		<?php endif; ?>
		<nav class="catalogPager__pages" aria-label="Страницы каталога">
			<?php for ( $i = 1; $i <= $max; $i++ ) : ?>
				<?php if ( $i === $paged ) : ?>
					<span class="catalogPager__page is-current" aria-current="page"><?php echo esc_html( (string) $i ); ?></span>
				<?php else : ?>
					<a class="catalogPager__page" href="<?php echo esc_url( okoyom_catalog_page_url( $i ) ); ?>"><?php echo esc_html( (string) $i ); ?></a>
				<?php endif; ?>
			<?php endfor; ?>
		</nav>
	</div>
	<?php
}

function okoyom_catalog_room_redirect(): void {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	if ( ! is_post_type_archive( 'product' ) && ! is_page( 'catalog' ) ) {
		return;
	}

	$query = array();
	parse_str( (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_QUERY ), $query );
	if ( array( 'room' ) !== array_keys( $query ) ) {
		return;
	}

	$raw    = is_array( $query['room'] ) ? $query['room'] : explode( ',', (string) $query['room'] );
	$values = array_filter( array_map( 'sanitize_title', array_map( 'strval', $raw ) ) );
	$slugs  = array_unique( array_filter( array_map( 'okoyom_room_value_to_slug', $values ) ) );
	if ( 1 !== count( $slugs ) ) {
		return;
	}

	$slug = (string) reset( $slugs );

	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$link = get_term_link( $term );
	if ( is_wp_error( $link ) ) {
		return;
	}

	wp_safe_redirect( $link, 301 );
	exit;
}
add_action( 'template_redirect', 'okoyom_catalog_room_redirect' );

function okoyom_service_cat_redirect(): void {
	if ( ! is_tax( 'product_cat' ) || ! function_exists( 'okoyom_term_is_service' ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || ! okoyom_term_is_service( $term->taxonomy, (int) $term->term_id ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/catalog/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'okoyom_service_cat_redirect', 4 );

function okoyom_is_catalog_view(): bool {
	return is_post_type_archive( 'product' )
		|| is_tax( array( 'product_cat', 'oko_collection', 'oko_series', 'oko_subject', 'oko_color' ) )
		|| is_page( array( 'catalog', 'search' ) );
}

function okoyom_catalog_main_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_post_type_archive( 'product' ) && ! $query->is_tax( array( 'product_cat', 'oko_collection', 'oko_series', 'oko_subject', 'oko_color' ) ) ) {
		return;
	}

	$query->set( 'posts_per_page', okoyom_catalog_per_page() );
}
add_action( 'pre_get_posts', 'okoyom_catalog_main_query' );

function okoyom_catalog_paged_404(): void {
	if ( is_admin() || ! okoyom_is_catalog_view() ) {
		return;
	}

	if ( ! okoyom_catalog_is_paginated() ) {
		return;
	}

	$paged = okoyom_catalog_paged();
	if ( $paged < 2 || $paged <= okoyom_catalog_max_pages( 'all', okoyom_catalog_search() ) ) {
		return;
	}

	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'okoyom_catalog_paged_404', 5 );

function okoyom_catalog_paged_title( $title ) {
	static $done = false;

	$paged = okoyom_catalog_paged();
	if ( $done || $paged < 2 || ! okoyom_is_catalog_view() || is_404() ) {
		return $title;
	}

	$done = true;

	return $title . ' — страница ' . $paged;
}
add_filter( 'rank_math/frontend/title', 'okoyom_catalog_paged_title', 20 );
add_filter( 'pre_get_document_title', 'okoyom_catalog_paged_title', 20 );

function okoyom_catalog_paged_canonical( $canonical ) {
	if ( ! okoyom_is_catalog_view() || is_404() ) {
		return $canonical;
	}

	return home_url( okoyom_catalog_page_url( min( okoyom_catalog_paged(), okoyom_catalog_max_pages( 'all', okoyom_catalog_search() ) ), true ) );
}
add_filter( 'rank_math/frontend/canonical', 'okoyom_catalog_paged_canonical', 20 );


function okoyom_crumb_text( string $text ): string {
	return trim( html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' ) );
}

function okoyom_breadcrumb_trail(): array {
	$home    = okoyom_crumb_text( okoyom_t( '047f5653b183', 'Главная' ) );
	$catalog = okoyom_crumb_text( okoyom_t( 'ad51225e2ef0', 'Каталог' ) );
	$trail   = array( array( 'name' => $home, 'url' => '/' ) );

	if ( is_singular( 'product' ) ) {
		$trail[] = array( 'name' => $catalog, 'url' => '/catalog/' );

		$room = okoyom_room_value_to_slug( isset( $_GET['room'] ) ? sanitize_title( wp_unslash( (string) $_GET['room'] ) ) : '' );
		if ( '' !== $room ) {
			$term = get_term_by( 'slug', $room, 'product_cat' );
			if ( $term instanceof WP_Term ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					$trail[] = array( 'name' => okoyom_crumb_text( $term->name ), 'url' => (string) wp_parse_url( $link, PHP_URL_PATH ) );
				}
			}
		}

		$trail[] = array( 'name' => okoyom_crumb_text( get_the_title() ), 'url' => '' );

		return $trail;
	}

	if ( is_page( 'search' ) ) {
		$trail[] = array( 'name' => $catalog, 'url' => '/catalog/' );
		$trail[] = array( 'name' => 'Поиск', 'url' => '' );

		return $trail;
	}

	$paged = okoyom_catalog_paged();
	$term  = is_tax() ? get_queried_object() : null;

	if ( $term instanceof WP_Term ) {
		$trail[] = array( 'name' => $catalog, 'url' => '/catalog/' );
		$trail[] = array(
			'name' => okoyom_crumb_text( $term->name ),
			'url'  => $paged > 1 ? okoyom_catalog_page_url( 1, true ) : '',
		);
	} else {
		$trail[] = array(
			'name' => $catalog,
			'url'  => $paged > 1 ? okoyom_catalog_page_url( 1, true ) : '',
		);
	}

	if ( $paged > 1 ) {
		$trail[] = array( 'name' => 'Страница ' . $paged, 'url' => '' );
	}

	return $trail;
}

function okoyom_breadcrumbs(): void {
	if ( ! okoyom_is_catalog_view() && ! is_singular( 'product' ) ) {
		return;
	}
	?>
	<div class="breadLinks">
		<?php foreach ( okoyom_breadcrumb_trail() as $i => $crumb ) : ?>
			<?php if ( $i > 0 ) : ?>
				<span>/</span>
			<?php endif; ?>
			<?php if ( '' !== $crumb['url'] ) : ?>
				<a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['name'] ); ?></a>
			<?php else : ?>
				<p><?php echo esc_html( $crumb['name'] ); ?></p>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php
}

function okoyom_breadcrumbs_schema(): void {
	if ( ! okoyom_is_catalog_view() && ! is_singular( 'product' ) ) {
		return;
	}

	$items = array();
	foreach ( okoyom_breadcrumb_trail() as $i => $crumb ) {
		$item = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb['name'],
		);

		if ( '' !== $crumb['url'] ) {
			$item['item'] = home_url( $crumb['url'] );
		}

		$items[] = $item;
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $items,
			),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);
}
add_action( 'wp_head', 'okoyom_breadcrumbs_schema', 6 );
