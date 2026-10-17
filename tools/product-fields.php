<?php
/**
 * Артикулы и цена товаров (пп.4-5 правок 17.10).
 * Предпросмотр: php tools/product-fields.php
 * Применение:   php tools/product-fields.php --apply
 */

defined( 'ABSPATH' ) || require dirname( __DIR__ ) . '/wp/wp-load.php';

$apply = in_array( '--apply', $argv, true );
echo $apply ? "РЕЖИМ: применение\n\n" : "РЕЖИМ: предпросмотр (--apply чтобы применить)\n\n";

$price = (float) apply_filters( 'okoyom_product_base_price', 7000 );

$ids = get_posts(
	array(
		'post_type'      => 'product',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

$sku_set   = 0;
$sku_keep  = 0;
$sku_empty = 0;
$price_set = 0;

printf( "товаров: %d, цена за м²: %s\n\n", count( $ids ), number_format_i18n( $price ) );

foreach ( $ids as $id ) {
	$title = (string) get_post_field( 'post_title', $id, 'raw' );
	$sku   = (string) get_post_meta( $id, '_sku', true );
	$want  = okoyom_product_sku_from_title( $title );

	if ( '' === $want ) {
		++$sku_empty;
		printf( "  %-34s артикул из названия не вывести\n", mb_substr( $title, 0, 34 ) );
	} elseif ( $sku === $want ) {
		++$sku_keep;
	} else {
		++$sku_set;
		printf( "  %-34s артикул %s%s\n", mb_substr( $title, 0, 34 ), '' !== $sku ? $sku . ' -> ' : '', $want );
		if ( $apply ) {
			update_post_meta( $id, '_sku', $want );
		}
	}

	$current = (float) get_post_meta( $id, '_price', true );
	if ( abs( $current - $price ) > 0.001 ) {
		++$price_set;
		if ( $apply ) {
			update_post_meta( $id, '_regular_price', $price );
			update_post_meta( $id, '_price', $price );
		}
	}
}

printf( "\nартикулы: проставлено %d, совпадали %d, не вывести %d\n", $sku_set, $sku_keep, $sku_empty );
printf( "цена: проставлена у %d товаров\n", $price_set );

if ( $apply && function_exists( 'wc_delete_product_transients' ) ) {
	foreach ( $ids as $id ) {
		wc_delete_product_transients( $id );
	}
	echo "кэш товаров сброшен\n";
}

echo $apply ? "\nГотово.\n" : "\nНичего не менялось.\n";
