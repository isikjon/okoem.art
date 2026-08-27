<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_IMAGES_OPTION = 'okoyom_image_overrides';

function okoyom_replaceable_images(): array {
	$dir = get_template_directory() . '/assets/img';
	if ( ! is_dir( $dir ) ) {
		return array();
	}
	$files = glob( $dir . '/*.{webp,jpg,jpeg,png}', GLOB_BRACE ) ?: array();
	$out   = array();
	foreach ( $files as $file ) {
		if ( filesize( $file ) < 25000 || 'icon' === pathinfo( $file, PATHINFO_FILENAME ) ) {
			continue;
		}
		$out[] = basename( $file );
	}
	sort( $out );
	return $out;
}

function okoyom_image_overrides(): array {
	return (array) get_option( OKOYOM_IMAGES_OPTION, array() );
}

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			'Картинки сайта',
			'Картинки сайта',
			'manage_options',
			'okoyom-images',
			'okoyom_render_images_page',
			'dashicons-format-image',
			59
		);
	}
);

add_action(
	'admin_enqueue_scripts',
	function ( string $hook ) {
		if ( 'toplevel_page_okoyom-images' === $hook ) {
			wp_enqueue_media();
		}
	}
);

add_action(
	'admin_init',
	function () {
		if ( ! isset( $_POST['okoyom_images_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['okoyom_images_nonce'] ), 'okoyom_images_save' )
			|| ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$submitted = isset( $_POST['okoyom_image'] ) && is_array( $_POST['okoyom_image'] ) ? wp_unslash( $_POST['okoyom_image'] ) : array();
		$clean     = array();
		foreach ( okoyom_replaceable_images() as $name ) {
			$url = esc_url_raw( (string) ( $submitted[ $name ] ?? '' ) );
			if ( $url ) {
				$clean[ $name ] = $url;
			}
		}
		update_option( OKOYOM_IMAGES_OPTION, $clean );
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-success is-dismissible"><p>Картинки сохранены.</p></div>';
			}
		);
	}
);

function okoyom_render_images_page(): void {
	$overrides = okoyom_image_overrides();
	$base      = get_template_directory_uri() . '/assets/img/';
	?>
	<div class="wrap">
		<h1>Картинки сайта</h1>
		<p class="description">Замена изображений из вёрстки (которые не загружаются через товары и записи). Нажмите «Выбрать», выберите картинку в медиатеке. Пусто — остаётся картинка из вёрстки.</p>
		<form method="post">
			<?php wp_nonce_field( 'okoyom_images_save', 'okoyom_images_nonce' ); ?>
			<table class="widefat striped" style="max-width:900px">
				<thead><tr><th style="width:120px">Из вёрстки</th><th>Замена</th></tr></thead>
				<tbody>
				<?php foreach ( okoyom_replaceable_images() as $name ) : $val = $overrides[ $name ] ?? ''; ?>
					<tr>
						<td><img src="<?php echo esc_url( $base . $name ); ?>" style="width:100px;height:auto;border:1px solid #ddd"><br><code style="font-size:10px"><?php echo esc_html( $name ); ?></code></td>
						<td>
							<div class="okoyom-img-row" style="display:flex;gap:10px;align-items:center">
								<img class="okoyom-img-preview" src="<?php echo esc_url( $val ); ?>" style="width:100px;height:auto;<?php echo $val ? '' : 'display:none'; ?>;border:1px solid #ddd">
								<input type="url" class="regular-text okoyom-img-input" name="okoyom_image[<?php echo esc_attr( $name ); ?>]" value="<?php echo esc_attr( $val ); ?>" placeholder="URL изображения">
								<button type="button" class="button okoyom-img-pick">Выбрать</button>
								<button type="button" class="button okoyom-img-clear">Убрать</button>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<p><button class="button button-primary">Сохранить картинки</button></p>
		</form>
	</div>
	<script>
	jQuery(function($){
		$('.okoyom-img-pick').on('click', function(e){
			e.preventDefault();
			var row = $(this).closest('.okoyom-img-row');
			var frame = wp.media({ title: 'Выберите изображение', multiple: false, library: { type: 'image' } });
			frame.on('select', function(){
				var a = frame.state().get('selection').first().toJSON();
				row.find('.okoyom-img-input').val(a.url);
				row.find('.okoyom-img-preview').attr('src', a.url).show();
			});
			frame.open();
		});
		$('.okoyom-img-clear').on('click', function(e){
			e.preventDefault();
			var row = $(this).closest('.okoyom-img-row');
			row.find('.okoyom-img-input').val('');
			row.find('.okoyom-img-preview').hide();
		});
	});
	</script>
	<?php
}

add_action(
	'template_redirect',
	function () {
		$overrides = okoyom_image_overrides();
		if ( ! $overrides || is_admin() ) {
			return;
		}
		ob_start(
			function ( string $html ) use ( $overrides ): string {
				$base = get_template_directory_uri() . '/assets/img/';
				foreach ( $overrides as $name => $url ) {
					$html = str_replace( $base . $name, $url, $html );
				}
				return $html;
			}
		);
	},
	1
);
