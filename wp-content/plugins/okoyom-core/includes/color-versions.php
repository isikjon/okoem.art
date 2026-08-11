<?php

defined( 'ABSPATH' ) || exit;

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_okoyom_colors',
				'title'    => 'Цветовые версии',
				'fields'   => array(
					array(
						'key'          => 'field_okoyom_colors',
						'label'        => 'Версии цвета',
						'name'         => 'okoyom_color_versions',
						'type'         => 'repeater',
						'instructions' => 'Кружки «Цветовое решение» на карточке. Первая версия — по умолчанию.',
						'layout'       => 'table',
						'button_label' => 'Добавить цвет',
						'sub_fields'   => array(
							array(
								'key'   => 'field_okoyom_color_title',
								'label' => 'Название',
								'name'  => 'title',
								'type'  => 'text',
							),
							array(
								'key'   => 'field_okoyom_color_hex',
								'label' => 'Цвет',
								'name'  => 'hex',
								'type'  => 'color_picker',
							),
							array(
								'key'           => 'field_okoyom_color_image',
								'label'         => 'Изображение',
								'name'          => 'image',
								'type'          => 'image',
								'return_format' => 'url',
								'preview_size'  => 'thumbnail',
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'product',
						),
					),
				),
			)
		);
	}
);

function okoyom_color_versions( int $product_id ): array {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}
	$rows = get_field( 'okoyom_color_versions', $product_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}
	$versions = array();
	foreach ( $rows as $row ) {
		$title = isset( $row['title'] ) ? (string) $row['title'] : '';
		$hex   = isset( $row['hex'] ) ? (string) $row['hex'] : '';
		if ( '' === $hex ) {
			continue;
		}
		$versions[] = array(
			'title' => $title,
			'hex'   => $hex,
			'image' => isset( $row['image'] ) ? (string) $row['image'] : '',
		);
	}
	return $versions;
}
