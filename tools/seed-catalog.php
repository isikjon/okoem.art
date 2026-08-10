<?php
/**
 * Наполнение каталога тестовыми данными (ТЗ п. 14: 3–5 карточек).
 *
 *     php tools/seed-catalog.php
 *
 * Идемпотентный: повторный запуск обновляет, не плодит дубли.
 * Категории, сюжеты, материалы, привязка основного материала к товару,
 * галереи из картинок макета и Figma-нарезок.
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
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function okoyom_say( string $m ): void { echo $m, PHP_EOL; }

if ( ! class_exists( 'WooCommerce' ) ) {
	fwrite( STDERR, "WooCommerce не активен.\n" );
	exit( 1 );
}

// --- термины ------------------------------------------------------------
function okoyom_term( string $taxonomy, string $name, string $slug ): int {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$new = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
	return is_wp_error( $new ) ? 0 : (int) $new['term_id'];
}

$cat_murals    = okoyom_term( 'product_cat', 'Муралы', 'murals' );
$cat_companion = okoyom_term( 'product_cat', 'Фоновые обои', 'companion' );
$subj_landsc   = okoyom_term( 'oko_subject', 'Пейзажи', 'landscapes' );
$subj_abstract = okoyom_term( 'oko_subject', 'Абстракция', 'abstract' );
$coll_silentia = okoyom_term( 'oko_collection', 'Silentia', 'silentia' );
okoyom_say( 'Термины готовы.' );

// --- материалы ----------------------------------------------------------
function okoyom_material( string $title, float $price, string $seam, int $strip = 0 ): int {
	$found = get_posts(
		array(
			'post_type'      => 'oko_material',
			'title'          => $title,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	$id = $found ? (int) $found[0] : (int) wp_insert_post(
		array(
			'post_type'   => 'oko_material',
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);

	update_post_meta( $id, '_okoyom_price_per_sqm', $price );
	update_post_meta( $id, '_okoyom_seam_type', $seam );
	update_post_meta( $id, '_okoyom_strip_width', $strip );

	return $id;
}

$mat_fliz  = okoyom_material( 'Флизелин премиум', 4500, 'seamless' );
$mat_vinyl = okoyom_material( 'Винил на флизелине', 3800, 'seam', 100 );
$mat_text  = okoyom_material( 'Текстильное покрытие', 5200, 'seamless' );
okoyom_say( 'Материалы: Флизелин 4500, Винил 3800, Текстиль 5200.' );

// --- картинки -----------------------------------------------------------
function okoyom_media( string $path, string $title ): int {
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_okoyom_seed_source',
			'meta_value'     => basename( $path ),
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}
	$up = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) );
	if ( ! empty( $up['error'] ) ) {
		return 0;
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $up['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$up['file']
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $up['file'] ) );
	update_post_meta( $id, '_okoyom_seed_source', basename( $path ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	return (int) $id;
}

// --- товары -------------------------------------------------------------
$products = array(
	array(
		'slug'       => 'dalnie-hrebty',
		'title'      => 'Дальние хребты',
		'sku'        => 'OKM-001',
		'excerpt'    => 'Туманные горы на рассвете. Многослойная композиция с плавными переходами тонов создаёт ощущение бесконечной глубины.',
		'category'   => $cat_murals,
		'subject'    => $subj_landsc,
		'collection' => $coll_silentia,
		'main'       => $mat_fliz,
		'extra'      => array( $mat_vinyl, $mat_text ),
		'images'     => array(
			array( $root . '/assets-inbox/01-gory-vertikal.png', 'Дальние хребты — фрагмент' ),
			array( $root . '/assets-inbox/05-gory-vertikal-b.png', 'Дальние хребты — фрагмент 2' ),
			array( $root . '/assets-inbox/07-gory-vertikal-c.png', 'Дальние хребты — фрагмент 3' ),
		),
	),
	array(
		'slug'       => 'terrakotovye-volny',
		'title'      => 'Терракотовые волны',
		'sku'        => 'OKM-002',
		'excerpt'    => 'Тёплая абстракция в терракотовой гамме. Мягкие волны и песочные формы для спокойных интерьеров.',
		'category'   => $cat_murals,
		'subject'    => $subj_abstract,
		'collection' => $coll_silentia,
		'main'       => $mat_fliz,
		'extra'      => array( $mat_vinyl ),
		'images'     => array(
			array( $root . '/macket/img/img.png', 'Терракотовые волны' ),
			array( $root . '/macket/img/left-flexSectionAboutFreeSections.png', 'Терракотовые волны — фрагмент' ),
		),
	),
	array(
		'slug'       => 'tihie-vershiny',
		'title'      => 'Тихие вершины',
		'sku'        => 'OKM-003',
		'excerpt'    => 'Серый мрамор с глубокими прожилками. Строгая текстура для выразительных стен.',
		'category'   => $cat_companion,
		'subject'    => $subj_abstract,
		'collection' => $coll_silentia,
		'main'       => $mat_vinyl,
		'extra'      => array( $mat_fliz ),
		'images'     => array(
			array( $root . '/assets-inbox/02-mramor-vertikal.png', 'Тихие вершины' ),
			array( $root . '/assets-inbox/03-mramor-vertikal-b.png', 'Тихие вершины — фрагмент' ),
		),
	),
);

foreach ( $products as $data ) {
	$post = get_page_by_path( $data['slug'], OBJECT, 'product' );
	if ( ! $post ) {
		okoyom_say( "  ! товар {$data['slug']} не найден" );
		continue;
	}
	$id = $post->ID;

	wp_update_post(
		array(
			'ID'           => $id,
			'post_title'   => $data['title'],
			'post_excerpt' => $data['excerpt'],
		)
	);

	update_post_meta( $id, '_sku', $data['sku'] );
	wp_set_object_terms( $id, array( $data['category'] ), 'product_cat' );
	wp_set_object_terms( $id, array( $data['subject'] ), 'oko_subject' );
	wp_set_object_terms( $id, array( $data['collection'] ), 'oko_collection' );

	// ТЗ п. 5.4: один основной материал обязателен, дополнительные — список.
	update_post_meta( $id, '_okoyom_main_material', $data['main'] );
	update_post_meta( $id, '_okoyom_extra_materials', $data['extra'] );

	$gallery = array();
	foreach ( $data['images'] as $i => $img ) {
		$media_id = okoyom_media( $img[0], $img[1] );
		if ( ! $media_id ) {
			continue;
		}
		if ( 0 === $i ) {
			set_post_thumbnail( $id, $media_id );
		} else {
			$gallery[] = $media_id;
		}
	}
	update_post_meta( $id, '_product_image_gallery', implode( ',', $gallery ) );

	okoyom_say( "  {$data['title']}: SKU {$data['sku']}, галерея " . ( count( $gallery ) + 1 ) . ' фото.' );
}

okoyom_say( 'Каталог наполнен.' );
