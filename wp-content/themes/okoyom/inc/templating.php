<?php

defined( 'ABSPATH' ) || exit;

function okoyom_static_pages(): array {
	return array(
		'about'       => 'about',
		'designers'   => 'designers',
		'buyers'      => 'buyers',
		'inspiration' => 'inspiration',
		'contacts'    => 'contact',
		'policy'      => 'polite',
		'favorites'   => 'like-1',
		'cart'        => 'bag-1',
		'thanks'      => 'thanks',
	);
}

function okoyom_cart_url(): string {
	return home_url( '/cart/' );
}

function okoyom_favorites_url(): string {
	return home_url( '/favorites/' );
}

function okoyom_static_part( string $name ): bool {
	$file = OKOYOM_DIR . '/template-parts/static/' . $name . '.php';

	if ( ! file_exists( $file ) ) {
		return false;
	}

	get_template_part( 'template-parts/static/' . $name );
	okoyom_static_scripts( $name );

	return true;
}

function okoyom_inspiration_count(): string {
	$count = function_exists( 'okoyom_inspiration_items' ) ? count( okoyom_inspiration_items() ) : 0;

	$last_two = $count % 100;
	$last     = $count % 10;

	if ( $last_two >= 11 && $last_two <= 14 ) {
		$word = 'объектов';
	} elseif ( 1 === $last ) {
		$word = 'объект';
	} elseif ( $last >= 2 && $last <= 4 ) {
		$word = 'объекта';
	} else {
		$word = 'объектов';
	}

	return $count . ' ' . $word;
}

function okoyom_inspiration_gallery(): void {
	$items = function_exists( 'okoyom_inspiration_items' ) ? okoyom_inspiration_items() : array();

	if ( ! $items ) {
		$fallback = OKOYOM_DIR . '/template-parts/static/inspiration.gallery.php';
		if ( file_exists( $fallback ) ) {
			include $fallback;
		}

		return;
	}

	foreach ( $items as $item ) {
		$image = get_the_post_thumbnail(
			$item,
			'full',
			array(
				'loading'  => 'lazy',
				'decoding' => 'async',
				'alt'      => get_the_title( $item ),
			)
		);

		if ( ! $image ) {
			continue;
		}

		$subtitle = get_post_meta( $item->ID, '_okoyom_subtitle', true );
		?>
		<div class="pinterest-item">
			<?php echo $image; ?>
			<div class="pinterest-overlay"></div>
			<div class="pinterest-content">
				<div class="pinterest-subtitle">
					<?php echo esc_html( $subtitle ); ?>
				</div>
				<div class="pinterest-title">
					<?php echo esc_html( get_the_title( $item ) ); ?>
				</div>
			</div>
		</div>
		<?php
	}
}

function okoyom_static_scripts( string $name ): void {
	$file = OKOYOM_DIR . '/template-parts/static/' . $name . '.scripts.php';

	if ( ! file_exists( $file ) ) {
		return;
	}

	add_action(
		'wp_footer',
		static function () use ( $file ) {
			include $file;
		},
		20
	);
}
