<?php
/**
 * Настройка WooCommerce под ТЗ Этапа 1.
 *
 *     php tools/seed-woo.php
 *
 * Идемпотентно. Что делает:
 *   - каталог переезжает на /catalog/ (ТЗ п. 11.5, вёрстка ссылается туда);
 *   - штатные страницы корзины и оформления Woo закрываются: на Этапе 1
 *     нет ни checkout, ни онлайн-оплаты (ТЗ п. 1), а наша корзина — своя;
 *   - категории Муралы и Фоновые обои (ТЗ п. 5.4);
 *   - три тестовых товара с ценами за м² из макета;
 *   - материал «Флизелин премиум» из справочника привязывается к товарам.
 */

declare( strict_types=1 );

$root = dirname( __DIR__ );

$_SERVER['HTTP_HOST']      = '127.0.0.1:8080';
$_SERVER['SERVER_NAME']    = '127.0.0.1';
$_SERVER['SERVER_PORT']    = '8080';
$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';

define( 'WP_USE_THEMES', false );
require_once $root . '/wp/wp-load.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	fwrite( STDERR, "WooCommerce не активен.\n" );
	exit( 1 );
}

function okoyom_say( string $message ): void {
	echo $message, PHP_EOL;
}

// --- страница каталога ---------------------------------------------------
$shop_id = (int) get_option( 'woocommerce_shop_page_id' );
if ( $shop_id && get_post_field( 'post_name', $shop_id ) !== 'catalog' ) {
	wp_update_post(
		array(
			'ID'         => $shop_id,
			'post_name'  => 'catalog',
			'post_title' => 'Каталог',
		)
	);
	okoyom_say( 'Каталог: /catalog/' );
}

// --- лишние страницы Woo -------------------------------------------------
// Черновик вместо публикации: страницы не пропадают из настроек Woo,
// но перестают отвечать на фронте и не попадают в sitemap.
foreach ( array( 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_myaccount_page_id' ) as $option ) {
	$page_id = (int) get_option( $option );
	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		wp_update_post(
			array(
				'ID'          => $page_id,
				'post_status' => 'draft',
			)
		);
		okoyom_say( 'Закрыта страница Woo: ' . get_post_field( 'post_name', $page_id ) );
	}
}

update_option( 'woocommerce_calc_taxes', 'no' );
update_option( 'woocommerce_currency', 'RUB' );

// --- категории -----------------------------------------------------------
$categories = array();
foreach ( array( 'murals' => 'Муралы', 'companions' => 'Фоновые обои' ) as $slug => $name ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		$created = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
		if ( is_wp_error( $created ) ) {
			okoyom_say( "Категория $name: " . $created->get_error_message() );
			continue;
		}
		$categories[ $slug ] = (int) $created['term_id'];
		okoyom_say( "Категория: $name" );
	} else {
		$categories[ $slug ] = (int) $term->term_id;
	}
}

// --- материал ------------------------------------------------------------
$material_id = 0;
$materials   = get_posts(
	array(
		'post_type'      => 'oko_material',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	)
);

if ( $materials ) {
	$material_id = (int) $materials[0];
} else {
	$material_id = wp_insert_post(
		array(
			'post_type'    => 'oko_material',
			'post_title'   => 'Флизелин премиум',
			'post_status'  => 'publish',
			'post_content' => 'Матовое покрытие с благородной текстурой. Идеально для жилых интерьеров.',
		)
	);
	update_post_meta( $material_id, '_okoyom_price_per_sqm', 4500 );
	update_post_meta( $material_id, '_okoyom_seam_type', 'seamless' );
	okoyom_say( 'Материал: Флизелин премиум, 4500 ₽/м²' );
}

// --- тестовые товары -----------------------------------------------------
// Названия и цены из референса; SKU обязателен по ТЗ п. 5.1.
$products = array(
	array( 'Дальние хребты', 'dalnie-hrebty', 'OKM-001', 4500, 'murals', 'flexGreyInfoBlockRow__big-1.webp' ),
	array( 'Терракотовые волны', 'terrakotovye-volny', 'OKM-002', 4200, 'murals', 'left-flexSectionAboutFreeSections.png' ),
	array( 'Тихие вершины', 'tihie-vershiny', 'OKM-003', 4800, 'companions', 'block-flexBuyersFirstSection.png' ),
);

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

foreach ( $products as [ $title, $slug, $sku, $price, $cat, $image_file ] ) {
	if ( get_page_by_path( $slug, OBJECT, 'product' ) ) {
		continue;
	}

	$product = new WC_Product_Simple();
	$product->set_name( $title );
	$product->set_slug( $slug );
	$product->set_sku( $sku );
	$product->set_regular_price( (string) $price );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product_id = $product->save();

	if ( isset( $categories[ $cat ] ) ) {
		wp_set_object_terms( $product_id, array( $categories[ $cat ] ), 'product_cat' );
	}

	update_post_meta( $product_id, '_okoyom_main_material', $material_id );

	$source = dirname( __DIR__ ) . '/macket/img/' . $image_file;
	if ( file_exists( $source ) ) {
		$upload = wp_upload_bits( $image_file, null, file_get_contents( $source ) );
		if ( empty( $upload['error'] ) ) {
			$attachment_id = wp_insert_attachment(
				array(
					'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
					'post_title'     => $title,
					'post_status'    => 'inherit',
				),
				$upload['file'],
				$product_id
			);
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
			set_post_thumbnail( $product_id, $attachment_id );
		}
	}

	okoyom_say( "Товар: $title ($sku), $price ₽/м², категория $cat" );
}

flush_rewrite_rules( false );

okoyom_say( 'Готово.' );
