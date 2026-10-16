<?php

defined( 'ABSPATH' ) || exit;

function okoyom_room_image_names( int $product_id ): array {
	$ids   = array();
	$thumb = (int) get_post_thumbnail_id( $product_id );
	if ( $thumb ) {
		$ids[] = $thumb;
	}

	foreach ( array_filter( explode( ',', (string) get_post_meta( $product_id, '_product_image_gallery', true ) ) ) as $attachment_id ) {
		$ids[] = (int) $attachment_id;
	}

	$names = array();
	foreach ( array_unique( $ids ) as $attachment_id ) {
		$file = get_attached_file( $attachment_id );
		if ( $file ) {
			$names[] = basename( $file );
		}
	}

	return $names;
}

function okoyom_room_slugs_for_product( int $product_id ): array {
	if ( ! function_exists( 'okoyom_room_tokens_map' ) ) {
		return array();
	}

	$names = okoyom_room_image_names( $product_id );
	if ( ! $names ) {
		return array();
	}

	$slugs = array();
	foreach ( okoyom_room_tokens_map() as $slug => $tokens ) {
		foreach ( $tokens as $token ) {
			$pattern = '~-v-interere-' . preg_quote( $token, '~' ) . '(-\d+)*(-scaled)?\.[a-z0-9]+$~i';
			foreach ( $names as $name ) {
				if ( preg_match( $pattern, $name ) ) {
					$slugs[] = $slug;
					continue 3;
				}
			}
		}
	}

	return $slugs;
}

function okoyom_sync_room_categories( int $product_id ): array {
	if ( ! function_exists( 'okoyom_room_tokens_map' ) ) {
		return array();
	}

	$rooms   = array_keys( okoyom_room_tokens_map() );
	$desired = okoyom_room_slugs_for_product( $product_id );

	$current = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'slugs' ) );
	if ( is_wp_error( $current ) ) {
		return array();
	}

	$keep  = array_values( array_diff( $current, $rooms ) );
	$final = array_values( array_unique( array_merge( $keep, $desired ) ) );

	sort( $current );
	$sorted = $final;
	sort( $sorted );
	if ( $current === $sorted ) {
		return $desired;
	}

	wp_set_object_terms( $product_id, $final, 'product_cat', false );

	return $desired;
}

function okoyom_sync_room_categories_on_save( $post_id, $post = null, $update = null ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	okoyom_sync_room_categories( (int) $post_id );
}
add_action( 'save_post_product', 'okoyom_sync_room_categories_on_save', 30, 3 );

function okoyom_room_h1_field_add(): void {
	?>
	<div class="form-field">
		<label for="okoyom_h1">Заголовок H1</label>
		<input type="text" name="okoyom_h1" id="okoyom_h1" value="">
		<p>Выводится в баннере под крупной надписью. Если пусто, берётся название рубрики.</p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'okoyom_room_h1_field_add' );

function okoyom_room_h1_field_edit( $term ): void {
	$value = (string) get_term_meta( $term->term_id, 'okoyom_h1', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="okoyom_h1">Заголовок H1</label></th>
		<td>
			<input type="text" name="okoyom_h1" id="okoyom_h1" value="<?php echo esc_attr( $value ); ?>" style="width:95%">
			<p class="description">Выводится в баннере под крупной надписью. Если пусто, берётся название рубрики.</p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'okoyom_room_h1_field_edit' );

function okoyom_room_h1_save( $term_id ): void {
	if ( ! isset( $_POST['okoyom_h1'] ) ) {
		return;
	}

	$value = sanitize_text_field( wp_unslash( (string) $_POST['okoyom_h1'] ) );
	if ( '' === $value ) {
		delete_term_meta( $term_id, 'okoyom_h1' );
		return;
	}

	update_term_meta( $term_id, 'okoyom_h1', $value );
}
add_action( 'created_product_cat', 'okoyom_room_h1_save' );
add_action( 'edited_product_cat', 'okoyom_room_h1_save' );

function okoyom_sync_room_categories_on_attachment( $meta, $attachment_id ) {
	$parent = (int) get_post_field( 'post_parent', $attachment_id );
	if ( $parent && 'product' === get_post_type( $parent ) ) {
		okoyom_sync_room_categories( $parent );
	}

	return $meta;
}
add_filter( 'wp_update_attachment_metadata', 'okoyom_sync_room_categories_on_attachment', 20, 2 );
