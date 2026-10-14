<?php
/**
 * Перевод слагов со старой схемы транслитерации на новую.
 * Предпросмотр: php tools/translit-rescheme.php
 * Применение:   php tools/translit-rescheme.php --apply
 *
 * Трогает только те слаги, которые в точности совпадают с результатом СТАРОЙ
 * схемы для своего заголовка. Всё, что задано руками, остаётся нетронутым.
 * Записи обновляются через wp_update_post, поэтому WordPress сам отдаёт 301
 * со старого адреса на новый.
 */

defined( 'ABSPATH' ) || require dirname( __DIR__ ) . '/wp/wp-load.php';

if ( ! function_exists( 'okoyom_translit_slug' ) ) {
	fwrite( STDERR, "Плагин okoyom-core не активен.\n" );
	exit( 1 );
}

$apply = in_array( '--apply', $argv, true );
echo $apply ? "РЕЖИМ: применение\n\n" : "РЕЖИМ: предпросмотр, ничего не меняется (--apply чтобы применить)\n\n";

$old_map = array(
	'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'g',  'д' => 'd',
	'е' => 'e',  'ё' => 'e',  'ж' => 'zh', 'з' => 'z',  'и' => 'i',
	'й' => 'y',  'к' => 'k',  'л' => 'l',  'м' => 'm',  'н' => 'n',
	'о' => 'o',  'п' => 'p',  'р' => 'r',  'с' => 's',  'т' => 't',
	'у' => 'u',  'ф' => 'f',  'х' => 'h',  'ц' => 'c',  'ч' => 'ch',
	'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',  'ы' => 'y',  'ь' => '',
	'э' => 'e',  'ю' => 'yu', 'я' => 'ya',
	'і' => 'i',  'ї' => 'yi', 'є' => 'ye', 'ґ' => 'g',  'ў' => 'u',
);

$old_slug = static function ( string $text ) use ( $old_map ): string {
	$map = okoyom_translit_symbols();
	foreach ( $old_map as $from => $to ) {
		$map[ $from ]                           = $to;
		$map[ mb_strtoupper( $from, 'UTF-8' ) ] = $to;
	}
	$slug = preg_replace( '~[^a-z0-9]+~', '-', mb_strtolower( strtr( $text, $map ), 'UTF-8' ) );

	return trim( (string) $slug, '-' );
};

$changed = 0;
$skipped = 0;

echo "=== ЗАПИСИ ===\n";
$posts = get_posts(
	array(
		'post_type'      => array_values( get_post_types( array( 'public' => true ), 'names' ) ),
		'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'inherit' ),
		'posts_per_page' => -1,
		'orderby'        => 'ID',
		'order'          => 'ASC',
	)
);

foreach ( $posts as $post ) {
	$was = $old_slug( $post->post_title );
	$now = okoyom_translit_slug( $post->post_title );

	if ( '' === $was || $was === $now ) {
		continue;
	}
	if ( $post->post_name !== $was ) {
		++$skipped;
		continue;
	}

	printf( "  [%s] %s\n      %s\n   -> %s\n", $post->post_type, $post->post_title, $post->post_name, $now );

	if ( $apply ) {
		$res = wp_update_post( array( 'ID' => $post->ID, 'post_name' => $now ), true );
		if ( is_wp_error( $res ) ) {
			printf( "      ОШИБКА: %s\n", $res->get_error_message() );
			continue;
		}
		printf( "      сохранено: %s\n", get_post_field( 'post_name', $post->ID ) );
	}

	++$changed;
}

echo "\n=== ТЕРМИНЫ ===\n";
foreach ( get_taxonomies( array( 'public' => true ), 'names' ) as $tax ) {
	$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		continue;
	}

	foreach ( $terms as $term ) {
		$was = $old_slug( $term->name );
		$now = okoyom_translit_slug( $term->name );

		if ( '' === $was || $was === $now ) {
			continue;
		}
		if ( $term->slug !== $was ) {
			++$skipped;
			continue;
		}

		printf( "  [%s] %s\n      %s\n   -> %s\n", $tax, $term->name, $term->slug, $now );

		if ( $apply ) {
			$res = wp_update_term( $term->term_id, $tax, array( 'slug' => $now ) );
			if ( is_wp_error( $res ) ) {
				printf( "      ОШИБКА: %s\n", $res->get_error_message() );
				continue;
			}
		}

		++$changed;
	}
}

if ( $apply ) {
	flush_rewrite_rules( false );
	printf( "\nГотово. Обновлено: %d. Оставлено как есть (задано вручную): %d\n", $changed, $skipped );
} else {
	printf( "\nБудет обновлено: %d. Оставлено как есть (задано вручную): %d\n", $changed, $skipped );
}
