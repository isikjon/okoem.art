<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_META_PRODUCT_H1 = '_okoyom_h1';

function okoyom_product_h1( int $product_id ): string {
	$h1 = trim( (string) get_post_meta( $product_id, OKOYOM_META_PRODUCT_H1, true ) );

	return '' !== $h1 ? $h1 : get_the_title( $product_id );
}

function okoyom_product_sku_from_title( string $title ): string {
	$title = html_entity_decode( wp_strip_all_tags( $title ), ENT_QUOTES, 'UTF-8' );
	$parts = preg_split( '~\s*[–—−‑‐-]\s*~u', $title );

	return is_array( $parts ) && count( $parts ) > 1 ? trim( (string) end( $parts ) ) : '';
}

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'okoyom-product-h1',
			'Заголовок H1',
			'okoyom_render_product_h1_box',
			'product',
			'normal',
			'high'
		);
	}
);

function okoyom_render_product_h1_box( WP_Post $post ): void {
	wp_nonce_field( 'okoyom_product_h1_save', 'okoyom_product_h1_nonce' );
	$value = (string) get_post_meta( $post->ID, OKOYOM_META_PRODUCT_H1, true );
	?>
	<p>
		<input type="text" name="okoyom_product_h1" id="okoyom_product_h1" value="<?php echo esc_attr( $value ); ?>" style="width:100%">
	</p>
	<p class="description">Выводится заголовком на странице товара. Если пусто, берётся название. В каталоге и в хлебных крошках остаётся название.</p>
	<?php
}

add_action(
	'save_post_product',
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['okoyom_product_h1_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_product_h1_nonce'] ), 'okoyom_product_h1_save' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$value = sanitize_text_field( wp_unslash( (string) ( $_POST['okoyom_product_h1'] ?? '' ) ) );

		if ( '' === $value ) {
			delete_post_meta( $post_id, OKOYOM_META_PRODUCT_H1 );
			return;
		}

		update_post_meta( $post_id, OKOYOM_META_PRODUCT_H1, $value );
	},
	20
);
