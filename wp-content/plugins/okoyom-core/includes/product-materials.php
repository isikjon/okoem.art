<?php

defined( 'ABSPATH' ) || exit;

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'okoyom-product-materials',
			'Материалы товара',
			'okoyom_render_product_materials_box',
			'product',
			'side',
			'high'
		);
	}
);

function okoyom_render_product_materials_box( WP_Post $post ): void {
	wp_nonce_field( 'okoyom_product_materials', 'okoyom_product_materials_nonce' );

	$materials = get_posts(
		array(
			'post_type'      => 'oko_material',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	if ( ! $materials ) {
		echo '<p>Сначала добавьте материалы: меню «Материалы» слева.</p>';
		return;
	}

	$main  = (int) get_post_meta( $post->ID, '_okoyom_main_material', true );
	$extra = get_post_meta( $post->ID, '_okoyom_extra_materials', true );
	$extra = is_array( $extra ) ? array_map( 'intval', $extra ) : array();
	?>
	<p>
		<label for="okoyom-main-material"><strong>Основной материал</strong></label><br>
		<select id="okoyom-main-material" name="okoyom_main_material" style="width:100%">
			<option value="">— не выбран —</option>
			<?php foreach ( $materials as $material ) : ?>
				<option value="<?php echo esc_attr( (string) $material->ID ); ?>" <?php selected( $main, $material->ID ); ?>>
					<?php echo esc_html( $material->post_title ); ?>
					(<?php echo esc_html( number_format( (float) get_post_meta( $material->ID, '_okoyom_price_per_sqm', true ), 0, ',', ' ' ) ); ?> ₽/м²)
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p style="color:#d63638;<?php echo $main ? 'display:none' : ''; ?>">
		Без основного материала цена на сайте не считается.
	</p>
	<p><strong>Дополнительные материалы</strong></p>
	<?php foreach ( $materials as $material ) : ?>
		<label style="display:block;margin:2px 0">
			<input type="checkbox" name="okoyom_extra_materials[]"
				value="<?php echo esc_attr( (string) $material->ID ); ?>"
				<?php checked( in_array( $material->ID, $extra, true ) ); ?>>
			<?php echo esc_html( $material->post_title ); ?>
		</label>
	<?php endforeach; ?>
	<?php
}

add_action(
	'save_post_product',
	function ( int $post_id ) {
		if ( ! isset( $_POST['okoyom_product_materials_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_product_materials_nonce'] ), 'okoyom_product_materials' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$main = absint( $_POST['okoyom_main_material'] ?? 0 );
		update_post_meta( $post_id, '_okoyom_main_material', $main );

		$extra = array_map( 'absint', (array) ( $_POST['okoyom_extra_materials'] ?? array() ) );
		$extra = array_values( array_diff( array_unique( $extra ), array( $main, 0 ) ) );
		update_post_meta( $post_id, '_okoyom_extra_materials', $extra );

		okoyom_sync_product_price( $post_id );
	}
);

function okoyom_material_price( int $material_id ): float {
	return $material_id ? max( 0, (float) get_post_meta( $material_id, '_okoyom_price_per_sqm', true ) ) : 0;
}

function okoyom_sync_product_price( int $product_id ): bool {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return false;
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return false;
	}

	$price = okoyom_material_price( (int) get_post_meta( $product_id, '_okoyom_main_material', true ) );
	$want  = $price > 0 ? wc_format_decimal( $price ) : '';

	if ( (string) $product->get_regular_price( 'edit' ) === (string) $want
		&& (string) $product->get_price( 'edit' ) === (string) $want ) {
		return false;
	}

	$product->set_regular_price( $want );
	$product->set_sale_price( '' );
	$product->set_price( $want );
	$product->save();

	return true;
}

function okoyom_sync_material_products( int $material_id ): int {
	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'numberposts'    => -1,
			'fields'         => 'ids',
			'meta_key'       => '_okoyom_main_material',
			'meta_value'     => (string) $material_id,
		)
	);

	$done = 0;
	foreach ( $ids as $id ) {
		if ( okoyom_sync_product_price( (int) $id ) ) {
			++$done;
		}
	}

	return $done;
}

add_action(
	'updated_post_meta',
	function ( $meta_id, $object_id, $meta_key ): void {
		if ( '_okoyom_price_per_sqm' !== $meta_key || 'oko_material' !== get_post_type( (int) $object_id ) ) {
			return;
		}

		okoyom_sync_material_products( (int) $object_id );
	},
	10,
	3
);
