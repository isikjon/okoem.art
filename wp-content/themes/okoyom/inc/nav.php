<?php

defined( 'ABSPATH' ) || exit;

class Okoyom_Flat_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

		if ( $depth > 0 ) {
			return;
		}

		$classes = in_array( 'current-menu-item', (array) $item->classes, true ) ? ' class="active"' : '';

		$output .= sprintf(
			'<a href="%s"%s>%s</a>',
			esc_url( $item->url ),
			$classes,
			esc_html( $item->title )
		);
	}
}

function okoyom_nav_fallback_items(): array {
	return array(
		'catalog'     => 'Каталог',
		'inspiration' => 'Вдохновение',
		'designers'   => 'Дизайнерам',
		'buyers'      => 'Покупателям',
		'about'       => 'О студии',
		'contacts'    => 'Контакты',
	);
}

function okoyom_nav( string $location, string $container_class ): void {
	if ( ! has_nav_menu( $location ) ) {
		echo '<div class="' . esc_attr( $container_class ) . '">';
		foreach ( okoyom_nav_fallback_items() as $slug => $title ) {
			printf(
				'<a href="%s">%s</a>',
				esc_url( home_url( '/' . $slug . '/' ) ),
				esc_html( $title )
			);
		}
		echo '</div>';

		return;
	}

	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => 'div',
			'container_class' => $container_class,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'walker'         => new Okoyom_Flat_Walker(),
			'fallback_cb'    => false,
		)
	);
}
