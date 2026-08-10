<?php
/**
 * Установка WordPress и наполнение структурой проекта.
 *
 *     php tools/seed.php
 *
 * Скрипт идемпотентный: повторный запуск не создаёт дублей, только
 * дописывает недостающее. Нужен, чтобы окружение поднималось одинаково
 * у любого разработчика и на staging, а не собиралось руками в админке.
 *
 * Каталог и карточка товара здесь не заводятся: по ним открыты вопросы
 * к заказчику (docs/questions.md), шаблоны будут переделываться.
 */

declare( strict_types=1 );

$root  = dirname( __DIR__ );
$wpDir = $root . '/wp';

if ( ! file_exists( $wpDir . '/wp-load.php' ) ) {
	fwrite( STDERR, "Сначала bash tools/setup-local.sh\n" );
	exit( 1 );
}

/** Страницы проекта: слаг => заголовок. Слаги совпадают с картой в inc/templating.php. */
const OKOYOM_PAGES = array(
	'about'       => 'О студии',
	'designers'   => 'Дизайнерам',
	'buyers'      => 'Покупателям',
	'inspiration' => 'Вдохновение',
	'contacts'    => 'Контакты',
	'policy'      => 'Политика конфиденциальности',
	'favorites'   => 'Избранное',
	'cart'        => 'Корзина',
	'thanks'      => 'Спасибо',
	'search'      => 'Результаты поиска',
);

/** Пункты главного меню: слаг страницы => подпись. */
const OKOYOM_MENU = array(
	'catalog'     => 'Каталог',
	'inspiration' => 'Вдохновение',
	'designers'   => 'Дизайнерам',
	'buyers'      => 'Покупателям',
	'about'       => 'О студии',
	'contacts'    => 'Контакты',
);

function okoyom_say( string $message ): void {
	echo $message, PHP_EOL;
}

$seeding  = in_array( '--seed', $argv, true );
$site_url = getenv( 'SITE_URL' ) ?: 'http://127.0.0.1:8080';

// В консоли нет ни хоста, ни запроса, и WordPress записывает siteurl пустым.
// После этого он считает себя неустановленным и умирает на любой загрузке.
$parts                    = parse_url( $site_url );
$_SERVER['HTTP_HOST']     = $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' );
$_SERVER['SERVER_NAME']   = $parts['host'];
$_SERVER['SERVER_PORT']   = (string) ( $parts['port'] ?? 80 );
$_SERVER['REQUEST_URI']   = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';

define( 'WP_USE_THEMES', false );

// Пока база пуста, WordPress на любой загрузке молча уводит на мастер
// установки. Поэтому установка идёт отдельным проходом с WP_INSTALLING,
// а наполнение — дочерним процессом уже на нормально загруженном ядре.
if ( ! $seeding ) {
	define( 'WP_INSTALLING', true );
}

require_once $wpDir . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

// --- установка ----------------------------------------------------------
if ( ! $seeding ) {
	if ( ! is_blog_installed() ) {
		$password = wp_generate_password( 16, false );
		$result   = wp_install( 'ОКОЁМ', 'admin', 'admin@okoyom.local', true, '', $password );

		if ( is_wp_error( $result ) ) {
			fwrite( STDERR, $result->get_error_message() . PHP_EOL );
			exit( 1 );
		}

		update_option( 'siteurl', $site_url );
		update_option( 'home', $site_url );

		okoyom_say( 'WordPress установлен.' );
		okoyom_say( '  адрес:  ' . $site_url );
		okoyom_say( '  логин:  admin' );
		okoyom_say( '  пароль: ' . $password );
	} else {
		okoyom_say( 'WordPress уже установлен.' );
	}

	$command = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' --seed';
	passthru( $command, $code );
	exit( $code );
}

// --- тема и плагин ------------------------------------------------------
if ( get_stylesheet() !== 'okoyom' ) {
	switch_theme( 'okoyom' );
	okoyom_say( 'Тема okoyom включена.' );
}

if ( ! is_plugin_active( 'okoyom-core/okoyom-core.php' ) ) {
	$error = activate_plugin( 'okoyom-core/okoyom-core.php' );
	okoyom_say( is_wp_error( $error )
		? 'Плагин не включился: ' . $error->get_error_message()
		: 'Плагин «Окоём — ядро» включён.' );
}

// --- ЧПУ ----------------------------------------------------------------
// ТЗ п. 11.5: канонический URL со слэшем на конце.
if ( get_option( 'permalink_structure' ) !== '/%postname%/' ) {
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules( false );
	okoyom_say( 'ЧПУ: /%postname%/' );
}

update_option( 'blogdescription', 'Студия авторских настенных муралов' );
update_option( 'timezone_string', 'Europe/Moscow' );

// --- страницы -----------------------------------------------------------
$created = 0;
foreach ( OKOYOM_PAGES as $slug => $title ) {
	if ( get_page_by_path( $slug ) ) {
		continue;
	}

	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_content' => '',
		)
	);
	++$created;
}
okoyom_say( $created ? "Создано страниц: $created" : 'Страницы уже заведены.' );

// --- главная ------------------------------------------------------------
$front = get_page_by_path( 'home' );
if ( ! $front ) {
	$front_id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_name'   => 'home',
			'post_title'  => 'Главная',
			'post_status' => 'publish',
		)
	);
	$front = get_post( $front_id );
}

if ( get_option( 'page_on_front' ) != $front->ID ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front->ID );
	okoyom_say( 'Главная назначена.' );
}

// --- меню ---------------------------------------------------------------
foreach ( array( 'primary' => 'Главное меню', 'footer' => 'Меню в подвале' ) as $location => $menu_name ) {
	$menu = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
		$menu    = wp_get_nav_menu_object( $menu_id );

		foreach ( OKOYOM_MENU as $slug => $label ) {
			$page = get_page_by_path( $slug );

			// Каталога как страницы пока нет — он появится вместе
			// с архивом товаров WooCommerce.
			wp_update_nav_menu_item(
				$menu->term_id,
				0,
				$page
					? array(
						'menu-item-title'     => $label,
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $page->ID,
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
					: array(
						'menu-item-title'  => $label,
						'menu-item-url'    => home_url( '/' . $slug . '/' ),
						'menu-item-type'   => 'custom',
						'menu-item-status' => 'publish',
					)
			);
		}

		okoyom_say( "Меню «$menu_name» создано." );
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ( $locations[ $location ] ?? 0 ) !== $menu->term_id ) {
		$locations[ $location ] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		okoyom_say( "Меню «$menu_name» назначено на «$location»." );
	}
}

// --- галерея «Вдохновение» -----------------------------------------------
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Кладёт файл из макета в медиабиблиотеку. Повторно не загружает: ищет
 * вложение по имени исходника.
 */
function okoyom_seed_image( string $source, string $title ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_okoyom_seed_source',
			'meta_value'     => basename( $source ),
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	if ( ! file_exists( $source ) ) {
		return 0;
	}

	$upload = wp_upload_bits( basename( $source ), null, file_get_contents( $source ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( ! $attachment_id ) {
		return 0;
	}

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
	);
	update_post_meta( $attachment_id, '_okoyom_seed_source', basename( $source ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );

	return (int) $attachment_id;
}

if ( post_type_exists( 'oko_inspiration' ) ) {
	$macket = dirname( __DIR__ ) . '/macket/img/';

	// Ровно тот же порядок плиток, что в макете, — чтобы после переноса
	// в CMS страница выглядела как прежде. Дальше снимки меняет
	// контент-менеджер в админке.
	$kinds = array(
		'A' => array( 'block-flexBuyersFirstSection.png', 'Интерьер', 'Современный минимализм' ),
		'B' => array( 'left-flexSectionAboutFreeSections.png', 'Архитектура', 'Натуральные текстуры' ),
	);
	$layout = str_split( 'ABABABABABABAAABAABAABABA' );

	$existing_count = count( okoyom_inspiration_items() );

	if ( $existing_count ) {
		okoyom_say( "Галерея «Вдохновение» уже наполнена: $existing_count плиток." );
	} else {
		$images = array();
		foreach ( $kinds as $key => $kind ) {
			$images[ $key ] = okoyom_seed_image( $macket . $kind[0], $kind[2] );
		}

		$added = 0;
		foreach ( $layout as $index => $key ) {
			list( , $subtitle, $title ) = $kinds[ $key ];

			$post_id = wp_insert_post(
				array(
					'post_type'   => 'oko_inspiration',
					'post_title'  => $title,
					'post_status' => 'publish',
					'menu_order'  => $index + 1,
				)
			);

			if ( ! $post_id ) {
				continue;
			}

			update_post_meta( $post_id, '_okoyom_subtitle', $subtitle );
			if ( $images[ $key ] ) {
				set_post_thumbnail( $post_id, $images[ $key ] );
			}
			++$added;
		}

		okoyom_say( "Галерея «Вдохновение»: добавлено плиток — $added" );
	}
}

okoyom_say( 'Готово: ' . home_url( '/' ) );
