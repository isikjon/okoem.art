<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_COLOR_HEX_META = 'okoyom_color_hex';

function okoyom_color_hex( int $term_id ): string {
	$hex = (string) get_term_meta( $term_id, OKOYOM_COLOR_HEX_META, true );

	return preg_match( '/^#[0-9a-fA-F]{6}$/', $hex ) ? $hex : '';
}

add_action(
	'oko_color_add_form_fields',
	function () {
		?>
		<div class="form-field">
			<label for="okoyom-color-hex">Образец цвета</label>
			<input type="text" name="okoyom_color_hex" id="okoyom-color-hex" value="" class="okoyom-color-field">
			<p>Кружок этого цвета в фильтре каталога. Выбирается на палитре.</p>
		</div>
		<?php
	}
);

add_action(
	'oko_color_edit_form_fields',
	function ( WP_Term $term ) {
		?>
		<tr class="form-field">
			<th scope="row"><label for="okoyom-color-hex">Образец цвета</label></th>
			<td>
				<input type="text" name="okoyom_color_hex" id="okoyom-color-hex"
					value="<?php echo esc_attr( okoyom_color_hex( $term->term_id ) ); ?>" class="okoyom-color-field">
				<p class="description">Кружок этого цвета в фильтре каталога. Выбирается на палитре.</p>
			</td>
		</tr>
		<?php
	}
);

function okoyom_save_color_hex( int $term_id ): void {
	if ( ! isset( $_POST['okoyom_color_hex'] ) || ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	$hex = sanitize_hex_color( wp_unslash( $_POST['okoyom_color_hex'] ) );

	if ( $hex ) {
		update_term_meta( $term_id, OKOYOM_COLOR_HEX_META, $hex );
	} else {
		delete_term_meta( $term_id, OKOYOM_COLOR_HEX_META );
	}
}

add_action( 'created_oko_color', 'okoyom_save_color_hex' );
add_action( 'edited_oko_color', 'okoyom_save_color_hex' );

add_filter(
	'manage_edit-oko_color_columns',
	function ( array $columns ): array {
		$out = array();
		foreach ( $columns as $key => $label ) {
			$out[ $key ] = $label;
			if ( 'name' === $key ) {
				$out['okoyom_swatch'] = 'Образец';
			}
		}

		return $out;
	}
);

add_filter(
	'manage_oko_color_custom_column',
	function ( string $content, string $column, int $term_id ): string {
		if ( 'okoyom_swatch' !== $column ) {
			return $content;
		}

		$hex = okoyom_color_hex( $term_id );

		return $hex
			? '<span style="display:inline-block;width:22px;height:22px;border-radius:50%;border:1px solid rgba(0,0,0,.15);background:' . esc_attr( $hex ) . '"></span>'
			: '<span style="color:#a7aaad">—</span>';
	},
	10,
	3
);

add_action(
	'admin_enqueue_scripts',
	function ( string $hook ): void {
		$screen   = get_current_screen();
		$taxonomy = $screen ? $screen->taxonomy : '';

		if ( 'edit-tags.php' === $hook || 'term.php' === $hook ) {
			if ( 'oko_color' !== $taxonomy ) {
				return;
			}
			wp_enqueue_style( 'wp-color-picker' );
			wp_add_inline_script(
				'wp-color-picker',
				'jQuery(function($){$(".okoyom-color-field").wpColorPicker();});',
				'after'
			);
		}
	}
);

add_filter(
	'default_hidden_columns',
	function ( array $hidden, WP_Screen $screen ): array {
		if ( 'edit-product' !== $screen->id ) {
			return $hidden;
		}

		return array_values(
			array_unique(
				array_merge(
					$hidden,
					array(
						'global_unique_id',
						'product_tag',
						'taxonomy-oko_collection',
						'taxonomy-oko_series',
						'taxonomy-oko_subject',
						'taxonomy-oko_color',
					)
				)
			)
		);
	},
	10,
	2
);
