<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_TEXTS_OPTION = 'okoyom_texts';

function okoyom_texts_registry(): array {
	static $registry = null;
	if ( null === $registry ) {
		$file     = OKOYOM_DIR . '/inc/texts-registry.php';
		$registry = file_exists( $file ) ? (array) include $file : array();
	}
	return $registry;
}

function okoyom_texts_overrides(): array {
	static $overrides = null;
	if ( null === $overrides ) {
		$overrides = (array) get_option( OKOYOM_TEXTS_OPTION, array() );
	}
	return $overrides;
}

function okoyom_t( string $key, string $default = '' ): string {
	$overrides = okoyom_texts_overrides();
	if ( isset( $overrides[ $key ] ) && '' !== $overrides[ $key ] ) {
		return esc_html( $overrides[ $key ] );
	}
	return esc_html( $default );
}

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			'Тексты сайта',
			'Тексты сайта',
			'manage_options',
			'okoyom-texts',
			'okoyom_render_texts_page',
			'dashicons-editor-textcolor',
			58
		);
	}
);

add_action(
	'admin_init',
	function () {
		if ( ! isset( $_POST['okoyom_texts_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_texts_nonce'] ), 'okoyom_texts_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$registry  = okoyom_texts_registry();
		$submitted = isset( $_POST['okoyom_text'] ) && is_array( $_POST['okoyom_text'] ) ? wp_unslash( $_POST['okoyom_text'] ) : array();
		$clean     = array();
		foreach ( $registry as $key => $default ) {
			$value = isset( $submitted[ $key ] ) ? sanitize_textarea_field( $submitted[ $key ] ) : '';
			if ( '' !== $value && $value !== $default ) {
				$clean[ $key ] = $value;
			}
		}
		update_option( OKOYOM_TEXTS_OPTION, $clean );

		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-success is-dismissible"><p>Тексты сохранены.</p></div>';
			}
		);
	}
);

function okoyom_render_texts_page(): void {
	$registry  = okoyom_texts_registry();
	$overrides = okoyom_texts_overrides();
	$search    = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	?>
	<div class="wrap">
		<h1>Тексты сайта</h1>
		<p class="description">
			Все текстовые блоки сайта. Одинаковый текст встречается один раз —
			меняете его здесь, и он обновляется везде, где повторяется.
			Пустое поле — остаётся текст из вёрстки (показан серым).
		</p>

		<form method="get" style="margin:15px 0">
			<input type="hidden" name="page" value="okoyom-texts">
			<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Поиск по тексту" class="regular-text">
			<button class="button">Найти</button>
			<?php if ( $search ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=okoyom-texts' ) ); ?>" class="button">Сбросить</a>
			<?php endif; ?>
		</form>

		<form method="post">
			<?php wp_nonce_field( 'okoyom_texts_save', 'okoyom_texts_nonce' ); ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th style="width:45%">Текст из вёрстки</th>
						<th>Ваш вариант</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$shown = 0;
					foreach ( $registry as $key => $default ) :
						if ( '' !== $search && false === mb_stripos( $default, $search ) ) {
							continue;
						}
						++$shown;
						$value = $overrides[ $key ] ?? '';
						?>
						<tr>
							<td style="color:#646970"><?php echo esc_html( $default ); ?></td>
							<td>
								<textarea name="okoyom_text[<?php echo esc_attr( $key ); ?>]"
									rows="1" class="large-text"
									placeholder="<?php echo esc_attr( $default ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( 0 === $shown ) : ?>
						<tr><td colspan="2">Ничего не найдено.</td></tr>
					<?php endif; ?>
				</tbody>
			</table>
			<p><button class="button button-primary">Сохранить тексты</button></p>
		</form>
	</div>
	<?php
}
