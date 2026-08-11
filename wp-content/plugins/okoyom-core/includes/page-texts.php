<?php

defined( 'ABSPATH' ) || exit;

function okoyom_text_pages(): array {
	return array( 'about', 'designers', 'buyers', 'contacts', 'inspiration' );
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$location = array();
		foreach ( okoyom_text_pages() as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$location[] = array(
					array(
						'param'    => 'page',
						'operator' => '==',
						'value'    => (string) $page->ID,
					),
				);
			}
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_okoyom_page_texts',
				'title'    => 'Тексты страницы',
				'fields'   => array(
					array(
						'key'          => 'field_okoyom_page_eyebrow',
						'label'        => 'Надзаголовок',
						'name'         => 'okoyom_eyebrow',
						'type'         => 'text',
						'instructions' => 'Мелкая подпись над заголовком. Пусто — остаётся текст из вёрстки.',
					),
					array(
						'key'          => 'field_okoyom_page_heading',
						'label'        => 'Заголовок H1',
						'name'         => 'okoyom_heading',
						'type'         => 'text',
						'instructions' => 'Крупный заголовок страницы. Пусто — остаётся текст из вёрстки.',
					),
				),
				'location' => $location ?: array( array( array( 'param' => 'page', 'operator' => '==', 'value' => '0' ) ) ),
			)
		);
	}
);

function okoyom_apply_page_texts( string $html ): string {
	if ( ! function_exists( 'get_field' ) || ! is_page() ) {
		return $html;
	}

	$id      = get_queried_object_id();
	$eyebrow = (string) get_field( 'okoyom_eyebrow', $id );
	$heading = (string) get_field( 'okoyom_heading', $id );

	if ( '' !== $heading ) {
		$html = preg_replace(
			'/(<h1[^>]*>)\s*[^<]+(<\/h1>)/u',
			'$1' . esc_html( $heading ) . '$2',
			$html,
			1
		);
	}

	if ( '' !== $eyebrow ) {
		$html = preg_replace(
			'/(<p class="text-titleCardSection">)\s*[^<]+(<\/p>)/u',
			'$1' . esc_html( $eyebrow ) . '$2',
			$html,
			1
		);
		$html = preg_replace(
			'/(<p class="title-inspirationTop__text">)\s*[^<]+(<\/p>)/u',
			'$1' . esc_html( $eyebrow ) . '$2',
			$html,
			1
		);
	}

	return $html;
}
