<?php
/**
 * Plugin Name: Окоём — ядро
 * Description: Контент-модель проекта: материалы, таксономии фильтров, лиды. Функциональность вынесена из темы, чтобы переживать смену вёрстки.
 * Version: 0.1.0
 * Requires PHP: 8.1
 * Text Domain: okoyom
 *
 * @package okoyom-core
 */

defined( 'ABSPATH' ) || exit;

define( 'OKOYOM_CORE_VERSION', '0.1.0' );
define( 'OKOYOM_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once OKOYOM_CORE_DIR . 'includes/settings.php';
require_once OKOYOM_CORE_DIR . 'includes/taxonomies.php';
require_once OKOYOM_CORE_DIR . 'includes/materials.php';
require_once OKOYOM_CORE_DIR . 'includes/leads.php';
require_once OKOYOM_CORE_DIR . 'includes/inspiration.php';
require_once OKOYOM_CORE_DIR . 'includes/leads-api.php';
require_once OKOYOM_CORE_DIR . 'includes/leads-retry.php';
require_once OKOYOM_CORE_DIR . 'includes/product-materials.php';
require_once OKOYOM_CORE_DIR . 'includes/color-versions.php';
require_once OKOYOM_CORE_DIR . 'includes/page-texts.php';
require_once OKOYOM_CORE_DIR . 'includes/admin-ui.php';
require_once OKOYOM_CORE_DIR . 'includes/images-webp.php';
require_once OKOYOM_CORE_DIR . 'includes/featured.php';

add_action(
	'admin_notices',
	function () {
		if ( class_exists( 'WooCommerce' ) ) {
			return;
		}

		echo '<div class="notice notice-error"><p>';
		echo esc_html( 'Плагин «Окоём — ядро» требует активного WooCommerce: товары и таксономии фильтров без него не регистрируются.' );
		echo '</p></div>';
	}
);

register_activation_hook(
	__FILE__,
	function () {
		okoyom_register_taxonomies();
		okoyom_register_material_cpt();
		okoyom_register_lead_cpt();
		okoyom_register_inspiration_cpt();
		flush_rewrite_rules();
	}
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
