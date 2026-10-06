<?php
/**
 * Перевод имён уже загруженных файлов в латиницу вместе со всеми размерами.
 * Предпросмотр: php tools/translit-media-files.php
 * Применение:   php tools/translit-media-files.php --apply
 * Обновляет _wp_attached_file, метаданные размеров и ссылки в контенте и метаполях.
 */

defined( 'ABSPATH' ) || require dirname( __DIR__ ) . '/wp/wp-load.php';

if ( ! function_exists( 'okoyom_translit_file_name' ) ) {
	fwrite( STDERR, "Плагин okoyom-core не активен: функции транслитерации недоступны.\n" );
	exit( 1 );
}

global $wpdb;

$apply = in_array( '--apply', $argv, true );
echo $apply ? "РЕЖИМ: применение\n\n" : "РЕЖИМ: предпросмотр, ничего не меняется (--apply чтобы применить)\n\n";

$uploads = wp_get_upload_dir();
$ids     = $wpdb->get_col(
	"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value REGEXP '[^ -~]' ORDER BY post_id"
);

$done = 0;

foreach ( $ids as $id ) {
	$relative = (string) get_post_meta( $id, '_wp_attached_file', true );
	if ( '' === $relative ) {
		continue;
	}

	$dir      = ltrim( dirname( $relative ), '.' );
	$file     = basename( $relative );
	$ext      = '';
	$dot      = strrpos( $file, '.' );
	$old_base = $file;
	if ( false !== $dot ) {
		$ext      = substr( $file, $dot );
		$old_base = substr( $file, 0, $dot );
	}

	$scaled = '';
	$stem   = $old_base;
	if ( str_ends_with( $old_base, '-scaled' ) ) {
		$scaled = '-scaled';
		$stem   = substr( $old_base, 0, -strlen( '-scaled' ) );
	}

	$new_stem = okoyom_translit_file_name( $stem . $ext );
	$new_stem = substr( $new_stem, 0, strrpos( $new_stem, '.' ) ?: strlen( $new_stem ) );
	if ( '' === $new_stem || $new_stem === $stem ) {
		continue;
	}

	$abs_dir = untrailingslashit( $uploads['basedir'] . ( '' !== $dir ? '/' . $dir : '' ) );
	if ( file_exists( $abs_dir . '/' . $new_stem . $scaled . $ext ) ) {
		$new_stem .= '-' . $id;
	}

	$old_base = $stem;
	$new_base = $new_stem;
	$siblings = glob( $abs_dir . '/' . $stem . '*' ) ?: array();

	printf( "  вложение %d\n      %s\n   -> %s\n      файлов: %d\n", $id, $file, $new_stem . $scaled . $ext, count( $siblings ) );

	if ( ! $apply ) {
		++$done;
		continue;
	}

	foreach ( $siblings as $path ) {
		$target = $abs_dir . '/' . $new_base . substr( basename( $path ), strlen( $old_base ) );
		if ( ! @rename( $path, $target ) ) {
			printf( "      ОШИБКА переименования: %s\n", basename( $path ) );
		}
	}

	$new_relative = ( '' !== $dir ? $dir . '/' : '' ) . $new_base . $scaled . $ext;
	update_post_meta( $id, '_wp_attached_file', $new_relative );

	$meta = wp_get_attachment_metadata( $id );
	if ( is_array( $meta ) ) {
		if ( isset( $meta['file'] ) ) {
			$meta['file'] = $new_relative;
		}
		if ( isset( $meta['original_image'] ) ) {
			$meta['original_image'] = $new_base . substr( $meta['original_image'], strlen( $old_base ) );
		}
		if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $key => $size ) {
				if ( isset( $size['file'] ) ) {
					$meta['sizes'][ $key ]['file'] = $new_base . substr( $size['file'], strlen( $old_base ) );
				}
			}
		}
		wp_update_attachment_metadata( $id, $meta );
	}

	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s", $old_base, $new_base, '%' . $wpdb->esc_like( $old_base ) . '%' ) );
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s AND meta_value NOT LIKE 'a:%%' AND meta_value NOT LIKE 'O:%%' AND meta_value NOT LIKE 's:%%'", $old_base, $new_base, '%' . $wpdb->esc_like( $old_base ) . '%' ) );

	printf( "      готово\n" );
	++$done;
}

if ( $apply ) {
	clean_post_cache( 0 );
	printf( "\nГотово. Переименовано вложений: %d\n", $done );
} else {
	printf( "\nБудет переименовано вложений: %d. Запусти с --apply чтобы применить.\n", $done );
}
