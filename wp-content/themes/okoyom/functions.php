<?php

defined( 'ABSPATH' ) || exit;

define( 'OKOYOM_VERSION', '0.1.0' );
define( 'OKOYOM_DIR', get_template_directory() );
define( 'OKOYOM_URI', get_template_directory_uri() );
define( 'OKOYOM_ASSETS_URI', OKOYOM_URI . '/assets' );

require_once OKOYOM_DIR . '/inc/setup.php';
require_once OKOYOM_DIR . '/inc/assets.php';
require_once OKOYOM_DIR . '/inc/nav.php';
require_once OKOYOM_DIR . '/inc/templating.php';
require_once OKOYOM_DIR . '/inc/filters.php';
require_once OKOYOM_DIR . '/inc/catalog.php';
require_once OKOYOM_DIR . '/inc/product-page.php';
require_once OKOYOM_DIR . '/inc/cart-page.php';
require_once OKOYOM_DIR . '/inc/favorites-page.php';
require_once OKOYOM_DIR . '/inc/seo.php';
