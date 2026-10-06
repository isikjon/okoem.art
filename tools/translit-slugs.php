<?php
/**
 * Перевод существующих слагов записей и терминов в латиницу.
 * Предпросмотр: php tools/translit-slugs.php
 * Применение:   php tools/translit-slugs.php --apply
 * Записи обновляются через wp_update_post, поэтому WordPress сам
 * запоминает старый слаг и отдаёт 301 со старого адреса на новый.
 */

defined( 'ABSPATH' ) || require dirname( __DIR__ ) . '/wp/wp-load.php';

if ( ! function_exists( 'okoyom_translit_slug' ) ) {
	fwrite( STDERR, "Плагин okoyom-core не активен: функции транслитерации недоступны.\n" );
	exit( 1 );
}

$apply = in_array( '--apply', $argv, true );
echo $apply ? "РЕЖИМ: применение\n\n" : "РЕЖИМ: предпросмотр, ничего не меняется (--apply чтобы применить)\n\n";

$is_latin = static function ( string $slug ): bool {
	$decoded = rawurldecode( $slug );

	return (bool) preg_match( '~^[a-z0-9._-]+$~', $decoded ) && ! okoyom_translit_needed( $decoded );
};

$changed = 0;

$post_types = get_post_types( array( 'public' => true ), 'names' );
$posts      = get_posts(
	array(
		'post_type'      => array_values( $post_types ),
		'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'inherit' ),
		'posts_per_page' => -1,
		'orderby'        => 'ID',
		'order'          => 'ASC',
	)
);

echo "=== ЗАПИСИ ===\n";
foreach ( $posts as $post ) {
	$current = rawurldecode( $post->post_name );
	if ( $is_latin( $post->post_name ) ) {
		continue;
	}

	$new = okoyom_translit_slug( '' !== $post->post_title ? $post->post_title : $current );
	if ( '' === $new || $new === $post->post_name ) {
		continue;
	}

	printf( "  [%s] %s\n      %s\n   -> %s\n", $post->post_type, $post->post_title, $current, $new );

	if ( $apply ) {
		$res = wp_update_post(
			array(
				'ID'        => $post->ID,
				'post_name' => $new,
			),
			true
		);
		if ( is_wp_error( $res ) ) {
			printf( "      ОШИБКА: %s\n", $res->get_error_message() );
			continue;
		}
		$saved = get_post_field( 'post_name', $post->ID );
		printf( "      сохранено: %s\n", $saved );
	}

	++$changed;
}

echo "\n=== ТЕРМИНЫ ===\n";
$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
foreach ( $taxonomies as $tax ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $tax,
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		continue;
	}

	foreach ( $terms as $term ) {
		if ( $is_latin( $term->slug ) ) {
			continue;
		}

		$new = okoyom_translit_slug( $term->name );
		if ( '' === $new || $new === $term->slug ) {
			continue;
		}

		$base = $new;
		$i    = 2;
		while ( get_term_by( 'slug', $new, $tax ) ) {
			$new = $base . '-' . $i;
			++$i;
		}

		printf( "  [%s] %s\n      %s\n   -> %s\n", $tax, $term->name, rawurldecode( $term->slug ), $new );

		if ( $apply ) {
			$res = wp_update_term( $term->term_id, $tax, array( 'slug' => $new ) );
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
	printf( "\nГотово. Обновлено: %d\n", $changed );
} else {
	printf( "\nБудет обновлено: %d. Запусти с --apply чтобы применить.\n", $changed );
}
