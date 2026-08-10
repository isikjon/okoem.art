<?php

defined( 'ABSPATH' ) || exit;

const OKOYOM_SETTINGS_OPTION = 'okoyom_settings';

function okoyom_settings_fields(): array {
	return array(
		'phone'          => array( 'Телефон', '+7 (495) 123-45-67', 'Показывается в шапке, подвале и на контактах.' ),
		'whatsapp'       => array( 'WhatsApp', '', 'Ссылка вида https://wa.me/79991234567. Пусто — кнопка ведёт на телефон.' ),
		'telegram'       => array( 'Telegram', '', 'Ссылка вида https://t.me/имя. Пусто — кнопка ведёт на телефон.' ),
		'lead_email'     => array( 'Email для заявок', '', 'Пусто — письма не отправляются, заявки копятся в админке (до доменной почты).' ),
		'subs_instagram' => array( 'Подписчики Instagram', '125K', '' ),
		'subs_pinterest' => array( 'Подписчики Pinterest', '45K', '' ),
		'subs_vk'        => array( 'Подписчики ВКонтакте', '89K', '' ),
		'subs_dzen'      => array( 'Подписчики Яндекс', '32K', '' ),
	);
}

function okoyom_option( string $key ): string {
	$saved  = (array) get_option( OKOYOM_SETTINGS_OPTION, array() );
	$fields = okoyom_settings_fields();

	if ( isset( $saved[ $key ] ) && '' !== trim( (string) $saved[ $key ] ) ) {
		return (string) $saved[ $key ];
	}

	return $fields[ $key ][1] ?? '';
}

function okoyom_phone_href(): string {
	return 'tel:' . preg_replace( '/[^+\d]/', '', okoyom_option( 'phone' ) );
}

function okoyom_messenger_href( string $key ): string {
	$url = okoyom_option( $key );

	return $url ? $url : okoyom_phone_href();
}

add_action(
	'admin_menu',
	function () {
		add_options_page(
			'Настройки Окоём',
			'Окоём',
			'manage_options',
			'okoyom-settings',
			'okoyom_render_settings_page'
		);
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'okoyom_settings_group',
			OKOYOM_SETTINGS_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => function ( $value ): array {
					$clean = array();
					foreach ( array_keys( okoyom_settings_fields() ) as $key ) {
						$raw = (string) ( $value[ $key ] ?? '' );
						$clean[ $key ] = in_array( $key, array( 'whatsapp', 'telegram' ), true )
							? esc_url_raw( $raw )
							: sanitize_text_field( $raw );
					}
					return $clean;
				},
			)
		);
	}
);

function okoyom_render_settings_page(): void {
	?>
	<div class="wrap">
		<h1>Настройки Окоём</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'okoyom_settings_group' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( okoyom_settings_fields() as $key => $field ) : ?>
					<tr>
						<th scope="row">
							<label for="okoyom-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label>
						</th>
						<td>
							<input type="text" class="regular-text"
								id="okoyom-<?php echo esc_attr( $key ); ?>"
								name="<?php echo esc_attr( OKOYOM_SETTINGS_OPTION ); ?>[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( okoyom_option( $key ) ); ?>"
								placeholder="<?php echo esc_attr( $field[1] ); ?>">
							<?php if ( $field[2] ) : ?>
								<p class="description"><?php echo esc_html( $field[2] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
