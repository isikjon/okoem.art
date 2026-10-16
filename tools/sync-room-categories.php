<?php
/**
 * Разовый прогон автопривязки товаров к рубрикам помещений.
 * Предпросмотр: php tools/sync-room-categories.php
 * Применение:   php tools/sync-room-categories.php --apply
 * Дальше привязка пересчитывается сама при каждом сохранении товара.
 */

defined( 'ABSPATH' ) || require dirname( __DIR__ ) . '/wp/wp-load.php';

if ( ! function_exists( 'okoyom_room_slugs_for_product' ) ) {
	fwrite( STDERR, "Плагин okoyom-core не активен.\n" );
	exit( 1 );
}

$apply = in_array( '--apply', $argv, true );
echo $apply ? "РЕЖИМ: применение\n\n" : "РЕЖИМ: предпросмотр, ничего не меняется (--apply чтобы применить)\n\n";

$rooms = array_keys( okoyom_room_tokens_map() );
$stat  = array_fill_keys( $rooms, 0 );
$moves = array();

$products = get_posts(
	array(
		'post_type'      => 'product',
		'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'orderby'        => 'ID',
		'order'          => 'ASC',
	)
);

foreach ( $products as $product_id ) {
	$desired = okoyom_room_slugs_for_product( (int) $product_id );
	$current = wp_get_object_terms( (int) $product_id, 'product_cat', array( 'fields' => 'slugs' ) );
	$current = is_wp_error( $current ) ? array() : array_values( array_intersect( $current, $rooms ) );

	sort( $desired );
	sort( $current );

	foreach ( $desired as $slug ) {
		++$stat[ $slug ];
	}

	if ( $desired !== $current ) {
		$add  = array_diff( $desired, $current );
		$drop = array_diff( $current, $desired );
		$moves[] = sprintf(
			"  %s\n      было: %s\n      станет: %s%s%s",
			get_the_title( $product_id ),
			$current ? implode( ', ', $current ) : '—',
			$desired ? implode( ', ', $desired ) : '—',
			$add ? "\n      добавится: " . implode( ', ', $add ) : '',
			$drop ? "\n      снимется: " . implode( ', ', $drop ) : ''
		);
	}

	if ( $apply ) {
		okoyom_sync_room_categories( (int) $product_id );
	}
}

echo "=== товаров обработано: " . count( $products ) . " ===\n\n";
echo "=== состав рубрик по именам файлов ===\n";
foreach ( $stat as $slug => $count ) {
	printf( "  %-26s %d\n", $slug, $count );
}

if ( $moves ) {
	printf( "\n=== изменения (%d товаров) ===\n%s\n", count( $moves ), implode( "\n", array_slice( $moves, 0, 30 ) ) );
} else {
	echo "\n  изменений нет, привязка уже совпадает\n";
}

if ( $apply ) {
	foreach ( $rooms as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term ) {
			wp_update_term_count_now( array( $term->term_id ), 'product_cat' );
		}
	}
	echo "\nГотово. Счётчики рубрик пересчитаны.\n";
}
