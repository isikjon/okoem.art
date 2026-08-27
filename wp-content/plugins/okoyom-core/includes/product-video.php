<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_VIDEO_META = '_okoyom_video';

function okoyom_product_video( int $product_id ): string {
	return (string) get_post_meta( $product_id, OKOYOM_VIDEO_META, true );
}

function okoyom_video_slide_html( string $url, string $title ): string {
	if ( '' === $url ) {
		return '';
	}

	if ( preg_match( '~(youtube\.com|youtu\.be|vimeo\.com)~i', $url ) ) {
		$embed = $url;
		if ( preg_match( '~youtu\.be/([\w-]+)~i', $url, $m ) ) {
			$embed = 'https://www.youtube.com/embed/' . $m[1];
		} elseif ( preg_match( '~youtube\.com/watch\?v=([\w-]+)~i', $url, $m ) ) {
			$embed = 'https://www.youtube.com/embed/' . $m[1];
		} elseif ( preg_match( '~vimeo\.com/(\d+)~i', $url, $m ) ) {
			$embed = 'https://player.vimeo.com/video/' . $m[1];
		}
		return sprintf(
			'<div class="swiper-slide"><div class="mural-video"><iframe src="%s" title="%s" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe></div></div>',
			esc_url( $embed ),
			esc_attr( $title )
		);
	}

	return sprintf(
		'<div class="swiper-slide"><video class="mural-video" src="%s" controls preload="metadata" playsinline></video></div>',
		esc_url( $url )
	);
}

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'okoyom-product-video',
			'Видео',
			function ( WP_Post $post ) {
				wp_nonce_field( 'okoyom_video_save', 'okoyom_video_nonce' );
				$url = okoyom_product_video( $post->ID );
				?>
				<input type="url" class="widefat" name="okoyom_video"
					value="<?php echo esc_attr( $url ); ?>"
					placeholder="https://youtu.be/... или ссылка на mp4">
				<p class="description">Ссылка на YouTube, Vimeo или прямой файл .mp4. Видео станет первым кадром в галерее товара.</p>
				<?php
			},
			'product',
			'side'
		);
	}
);

add_action(
	'save_post_product',
	function ( int $post_id ): void {
		if ( ! isset( $_POST['okoyom_video_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_video_nonce'] ), 'okoyom_video_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$url = esc_url_raw( wp_unslash( $_POST['okoyom_video'] ?? '' ) );
		if ( $url ) {
			update_post_meta( $post_id, OKOYOM_VIDEO_META, $url );
		} else {
			delete_post_meta( $post_id, OKOYOM_VIDEO_META );
		}
	}
);
