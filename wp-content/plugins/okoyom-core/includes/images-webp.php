<?php

defined( 'ABSPATH' ) || exit;

function okoyom_webp_supported(): bool {
	return function_exists( 'imagewebp' ) || class_exists( 'Imagick' );
}

function okoyom_convert_to_webp( string $file, string $mime ): ?string {
	if ( ! in_array( $mime, array( 'image/jpeg', 'image/png' ), true ) || ! okoyom_webp_supported() ) {
		return null;
	}

	$target = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $file );
	if ( ! $target || $target === $file ) {
		return null;
	}

	$editor = wp_get_image_editor( $file );
	if ( is_wp_error( $editor ) ) {
		return null;
	}

	$editor->set_quality( 82 );
	$saved = $editor->save( $target, 'image/webp' );

	if ( is_wp_error( $saved ) || empty( $saved['path'] ) || ! file_exists( $saved['path'] ) ) {
		return null;
	}

	return (string) $saved['path'];
}

add_filter(
	'wp_handle_upload',
	function ( array $upload ): array {
		$webp = okoyom_convert_to_webp( $upload['file'], $upload['type'] );
		if ( ! $webp ) {
			return $upload;
		}

		if ( file_exists( $upload['file'] ) ) {
			wp_delete_file( $upload['file'] );
		}

		$upload['file'] = $webp;
		$upload['url']  = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $upload['url'] );
		$upload['type'] = 'image/webp';

		return $upload;
	}
);

function okoyom_webp_migrate_batch( int $limit = 20 ): array {
	$ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => array( 'image/jpeg', 'image/png' ),
			'posts_per_page' => $limit,
			'fields'         => 'ids',
		)
	);

	$done = array();

	foreach ( $ids as $id ) {
		$path = get_attached_file( (int) $id );
		if ( ! $path || ! file_exists( $path ) ) {
			continue;
		}

		$webp = okoyom_convert_to_webp( $path, (string) get_post_mime_type( $id ) );
		if ( ! $webp ) {
			continue;
		}

		update_attached_file( (int) $id, $webp );
		wp_update_post(
			array(
				'ID'             => (int) $id,
				'post_mime_type' => 'image/webp',
			)
		);
		wp_update_attachment_metadata( (int) $id, wp_generate_attachment_metadata( (int) $id, $webp ) );
		wp_delete_file( $path );

		$done[] = basename( $webp );
	}

	return $done;
}
