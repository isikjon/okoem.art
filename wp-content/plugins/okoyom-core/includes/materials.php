<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_MATERIAL_CPT = 'oko_material';

const OKOYOM_META_PRICE       = '_okoyom_price_per_sqm';
const OKOYOM_META_SEAM_TYPE   = '_okoyom_seam_type';
const OKOYOM_META_STRIP_WIDTH = '_okoyom_strip_width';

const OKOYOM_SEAM_TYPES = array(
	'seamless' => 'Бесшовный',
	'seam'     => 'Шовный',
);

function okoyom_register_material_cpt(): void {
	register_post_type(
		OKOYOM_MATERIAL_CPT,
		array(
			'labels'       => array(
				'name'          => 'Материалы',
				'singular_name' => 'Материал',
				'add_new_item'  => 'Добавить материал',
				'edit_item'     => 'Редактирование материала',
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-layout',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'show_in_rest' => true,
		)
	);

	register_post_meta(
		OKOYOM_MATERIAL_CPT,
		OKOYOM_META_PRICE,
		array(
			'type'              => 'number',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => fn( $value ) => max( 0, (float) $value ),
			'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
		)
	);

	register_post_meta(
		OKOYOM_MATERIAL_CPT,
		OKOYOM_META_SEAM_TYPE,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => fn( $value ) => array_key_exists( $value, OKOYOM_SEAM_TYPES ) ? $value : 'seamless',
			'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
		)
	);

	register_post_meta(
		OKOYOM_MATERIAL_CPT,
		OKOYOM_META_STRIP_WIDTH,
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => fn( $value ) => max( 0, (int) $value ),
			'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
		)
	);
}

add_action( 'init', 'okoyom_register_material_cpt' );

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'okoyom-material-params',
			'Параметры материала',
			'okoyom_render_material_meta_box',
			OKOYOM_MATERIAL_CPT,
			'normal',
			'high'
		);
	}
);

function okoyom_render_material_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'okoyom_material_save', 'okoyom_material_nonce' );

	$price       = get_post_meta( $post->ID, OKOYOM_META_PRICE, true );
	$seam_type   = get_post_meta( $post->ID, OKOYOM_META_SEAM_TYPE, true ) ?: 'seamless';
	$strip_width = get_post_meta( $post->ID, OKOYOM_META_STRIP_WIDTH, true );
	?>
	<p>
		<label for="okoyom-price"><strong>Цена за м², ₽</strong></label><br>
		<input type="number" step="0.01" min="0" required
			id="okoyom-price" name="okoyom_price" value="<?php echo esc_attr( $price ); ?>">
	</p>
	<p>
		<label for="okoyom-seam-type"><strong>Тип материала</strong></label><br>
		<select id="okoyom-seam-type" name="okoyom_seam_type">
			<?php foreach ( OKOYOM_SEAM_TYPES as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $seam_type, $value ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="okoyom-strip-width"><strong>Ширина полосы, см</strong></label><br>
		<input type="number" step="1" min="0"
			id="okoyom-strip-width" name="okoyom_strip_width" value="<?php echo esc_attr( $strip_width ); ?>">
		<br><span class="description">Заполняется только для шовных материалов.</span>
	</p>
	<?php
}

add_action(
	'save_post_' . OKOYOM_MATERIAL_CPT,
	function ( int $post_id ) {
		if ( ! isset( $_POST['okoyom_material_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_material_nonce'] ), 'okoyom_material_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, OKOYOM_META_PRICE, max( 0, (float) ( $_POST['okoyom_price'] ?? 0 ) ) );

		$seam_type = sanitize_key( $_POST['okoyom_seam_type'] ?? 'seamless' );
		$seam_type = array_key_exists( $seam_type, OKOYOM_SEAM_TYPES ) ? $seam_type : 'seamless';
		update_post_meta( $post_id, OKOYOM_META_SEAM_TYPE, $seam_type );

		$strip_width = 'seam' === $seam_type ? max( 0, (int) ( $_POST['okoyom_strip_width'] ?? 0 ) ) : 0;
		update_post_meta( $post_id, OKOYOM_META_STRIP_WIDTH, $strip_width );
	}
);
