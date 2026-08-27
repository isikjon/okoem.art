<?php
/**
 * Транслитерация слагов терминов фильтрующих таксономий в латиницу,
 * чтобы ЧПУ-адреса были человекопонятными (/collection/tihie-miry/).
 * Запуск: php tools/translit-term-slugs.php  (из корня рядом с wp/)
 */

defined( 'ABSPATH' ) || require dirname( __DIR__ ) . '/wp/wp-load.php';

function okoyom_translit( string $text ): string {
	$map = array(
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
		'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
		'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
		'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
		'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
	);
	$text = mb_strtolower( $text, 'UTF-8' );
	$text = strtr( $text, $map );
	$text = preg_replace( '~[^a-z0-9]+~', '-', $text );
	return trim( (string) $text, '-' );
}

$taxonomies = array( 'oko_collection', 'oko_series', 'oko_subject', 'oko_color' );
$changed    = 0;

foreach ( $taxonomies as $tax ) {
	$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		continue;
	}
	foreach ( $terms as $term ) {
		// уже латиница без процентов — пропускаем
		if ( preg_match( '~^[a-z0-9-]+$~', $term->slug ) ) {
			continue;
		}
		$new = okoyom_translit( $term->name );
		if ( '' === $new ) {
			continue;
		}
		// уникальность
		$base = $new;
		$i    = 2;
		while ( get_term_by( 'slug', $new, $tax ) ) {
			$new = $base . '-' . $i;
			++$i;
		}
		$res = wp_update_term( $term->term_id, $tax, array( 'slug' => $new ) );
		if ( ! is_wp_error( $res ) ) {
			echo "{$tax}: {$term->name}: {$term->slug} -> {$new}\n";
			++$changed;
		}
	}
}

flush_rewrite_rules( false );
echo "Готово. Обновлено слагов: {$changed}\n";
