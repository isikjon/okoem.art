<?php
/**
 * Загрузка снимков галереи «Вдохновение» из папки в CMS.
 *
 *     php tools/import-gallery.php [папка]
 *
 * По умолчанию берёт файлы из assets-inbox/. Кладём туда экспорт из Figma —
 * скрипт заливает картинки в медиабиблиотеку и раскладывает по плиткам
 * галереи по кругу, в алфавитном порядке имён файлов.
 *
 * Повторный запуск не плодит дубли: файл с тем же именем переиспользуется.
 * Плитки при этом переназначаются заново — так можно менять состав, просто
 * поправив содержимое папки.
 */

declare( strict_types=1 );

$root  = dirname( __DIR__ );
$inbox = $argv[1] ?? $root . '/assets-inbox';

$_SERVER['HTTP_HOST']      = '127.0.0.1:8080';
$_SERVER['SERVER_NAME']    = '127.0.0.1';
$_SERVER['SERVER_PORT']    = '8080';
$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';

define( 'WP_USE_THEMES', false );
require_once $root . '/wp/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function okoyom_say( string $message ): void {
	echo $message, PHP_EOL;
}

if ( ! is_dir( $inbox ) ) {
	fwrite( STDERR, "Нет папки $inbox\n" );
	exit( 1 );
}

$files = array();
foreach ( scandir( $inbox ) as $name ) {
	if ( preg_match( '/\.(png|jpe?g|webp|gif)$/i', $name ) ) {
		$files[] = $inbox . '/' . $name;
	}
}
sort( $files );

if ( ! $files ) {
	okoyom_say( "В $inbox нет картинок. Положите туда экспорт из Figma и повторите." );
	exit( 0 );
}

okoyom_say( 'Найдено картинок: ' . count( $files ) );

/** Заливает файл в медиабиблиотеку, переиспользуя уже загруженный. */
function okoyom_media_id( string $path ): int {
	$name = basename( $path );

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_okoyom_seed_source',
			'meta_value'     => $name,
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	$upload = wp_upload_bits( $name, null, file_get_contents( $path ) );
	if ( ! empty( $upload['error'] ) ) {
		okoyom_say( "  ! $name: " . $upload['error'] );
		return 0;
	}

	$title = ucfirst( str_replace( array( '-', '_' ), ' ', pathinfo( $name, PATHINFO_FILENAME ) ) );

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( ! $attachment_id ) {
		return 0;
	}

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
	);
	update_post_meta( $attachment_id, '_okoyom_seed_source', $name );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );

	okoyom_say( "  загружено: $name" );

	return (int) $attachment_id;
}

$media = array();
foreach ( $files as $path ) {
	$id = okoyom_media_id( $path );
	if ( $id ) {
		$media[] = $id;
	}
}

if ( ! $media ) {
	fwrite( STDERR, "Ни одна картинка не загрузилась.\n" );
	exit( 1 );
}

$items = okoyom_inspiration_items();
if ( ! $items ) {
	fwrite( STDERR, "В галерее нет плиток — сначала php tools/seed.php\n" );
	exit( 1 );
}

// Раскладываем по кругу: картинок обычно меньше, чем плиток.
foreach ( $items as $index => $item ) {
	set_post_thumbnail( $item, $media[ $index % count( $media ) ] );
}

okoyom_say( sprintf( 'Разложено по %d плиткам, картинок в обороте: %d', count( $items ), count( $media ) ) );
okoyom_say( 'Проверить: ' . home_url( '/inspiration/' ) );
