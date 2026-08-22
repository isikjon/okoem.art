<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_FEATURED_META = '_okoyom_featured';

function okoyom_featured_products( int $limit = 4 ): array {
	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'menu_order date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'meta_key'       => OKOYOM_FEATURED_META,
			'meta_value'     => '1',
		)
	);

	return array_map( 'intval', $ids );
}

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'okoyom_featured',
			'Лучшая работа',
			'okoyom_featured_metabox',
			'product',
			'side',
			'high'
		);
	}
);

function okoyom_featured_metabox( WP_Post $post ): void {
	wp_nonce_field( 'okoyom_featured_save', 'okoyom_featured_nonce' );
	$checked = '1' === get_post_meta( $post->ID, OKOYOM_FEATURED_META, true );
	?>
	<label style="display:flex;gap:8px;align-items:flex-start;">
		<input type="checkbox" name="okoyom_featured" value="1" <?php checked( $checked ); ?>>
		<span>Показывать в блоке «Избранные работы» на главной</span>
	</label>
	<?php
}

add_action(
	'save_post_product',
	function ( int $post_id ): void {
		if ( ! isset( $_POST['okoyom_featured_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_featured_nonce'] ), 'okoyom_featured_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['okoyom_featured'] ) ) {
			update_post_meta( $post_id, OKOYOM_FEATURED_META, '1' );
		} else {
			delete_post_meta( $post_id, OKOYOM_FEATURED_META );
		}
	}
);
