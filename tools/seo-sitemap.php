<?php
/**
 * Карта сайта: архивы фильтров (п.8).
 * Предпросмотр: php tools/seo-sitemap.php
 * Применение:   php tools/seo-sitemap.php --apply
 */

defined( 'FS_METHOD' ) || define( 'FS_METHOD', 'direct' );
defined( 'ABSPATH' ) || require dirname( __DIR__ ) . '/wp/wp-load.php';

$apply = in_array( '--apply', $argv, true );
echo $apply ? "РЕЖИМ: применение\n\n" : "РЕЖИМ: предпросмотр (--apply чтобы применить)\n\n";

$taxonomies = array(
	'product_cat'    => 'Рубрики',
	'oko_collection' => 'Коллекции',
	'oko_series'     => 'Серии',
	'oko_subject'    => 'Сюжеты',
	'oko_color'      => 'Цвета',
);

$options = get_option( 'rank-math-options-sitemap', array() );
$total   = okoyom_catalog_total();

printf( "всего опубликованных товаров: %d\n\n", $total );

foreach ( $taxonomies as $taxonomy => $label ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
	if ( is_wp_error( $terms ) || ! $terms ) {
		continue;
	}

	$useful = 0;

	echo "=== {$label} ===\n";
	foreach ( $terms as $term ) {
		$count     = okoyom_term_product_count( $term->taxonomy, $term->term_id );
		$duplicate = okoyom_term_duplicates_catalog( $term->taxonomy, $term->term_id );
		$robots    = (array) get_term_meta( $term->term_id, 'rank_math_robots', true );
		$closed    = in_array( 'noindex', $robots, true );

		if ( ! $duplicate ) {
			++$useful;
		}

		$action = '';
		if ( $duplicate && ! $closed ) {
			$action = '-> закрыть';
		} elseif ( ! $duplicate && $closed ) {
			$action = '-> открыть';
		}

		printf( "  %-30s %3d товаров  %-12s %s\n", $term->slug, $count, $duplicate ? 'повтор' : '', $action );

		if ( ! $apply || '' === $action ) {
			continue;
		}

		if ( $duplicate ) {
			update_term_meta( $term->term_id, 'rank_math_robots', array( 'noindex' ) );
		} else {
			delete_term_meta( $term->term_id, 'rank_math_robots' );
		}
	}

	$key  = 'tax_' . $taxonomy . '_sitemap';
	$want = $useful > 0 ? 'on' : 'off';
	$now  = $options[ $key ] ?? 'off';

	printf( "  карта сайта: %s%s\n\n", $now, $now === $want ? '' : ' -> ' . $want );

	if ( $now !== $want ) {
		$options[ $key ] = $want;
	}
}

echo "=== СЛУЖЕБНЫЕ СТРАНИЦЫ ===\n";
foreach ( okoyom_closed_pages() as $slug ) {
	$page = get_page_by_path( $slug );
	if ( ! $page instanceof WP_Post ) {
		printf( "  %-14s страницы нет\n", $slug );
		continue;
	}

	$robots = (array) get_post_meta( $page->ID, 'rank_math_robots', true );
	$closed = in_array( 'noindex', $robots, true );

	printf( "  %-14s %s\n", $slug, $closed ? 'закрыта' : '-> закрыть' );

	if ( $apply && ! $closed ) {
		update_post_meta( $page->ID, 'rank_math_robots', array( 'noindex' ) );
	}
}

echo "\n";

if ( $apply ) {
	update_option( 'rank-math-options-sitemap', $options );
	flush_rewrite_rules( true );

	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%\_transient\_sitemap\_%rank\_math\_%' OR option_name LIKE '%\_transient\_rank\_math%'" );

	echo "Настройки записаны, правила и кэш карты сброшены.\n";
} else {
	echo "Ничего не менялось.\n";
}
