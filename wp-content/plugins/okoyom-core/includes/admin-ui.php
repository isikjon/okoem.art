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

add_action(
	'admin_head',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-product' !== $screen->id ) {
			return;
		}
		?>
		<style>
			.wp-list-table .column-taxonomy-oko_color,
			.wp-list-table .column-taxonomy-oko_subject { width: 14%; }
			.wp-list-table .column-taxonomy-oko_collection,
			.wp-list-table .column-taxonomy-oko_series { width: 10%; }
			.wp-list-table .column-sku { width: 8%; }
			.wp-list-table .column-price,
			.wp-list-table .column-date { width: 7%; }
			.wp-list-table .column-product_tag,
			.wp-list-table .column-featured,
			.wp-list-table .column-is_in_stock { width: 5%; }
		</style>
		<?php
	}
);
