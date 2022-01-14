<?php
/**
 * The DWS WordPress Framework WooCommerce bootstrap file.
 *
 * @since               1.0.0
 * @version             1.0.0
 * @package             DeepWebSolutions\WP-Framework\WooCommerce
 * @author              Deep Web Solutions GmbH
 * @copyright           2020 Deep Web Solutions GmbH
 * @license             GPL-3.0-or-later
 *
 * @noinspection PhpMissingReturnTypeInspection
 *
 * @wordpress-plugin
 * Plugin Name:             DWS WordPress Framework WooCommerce
 * Description:             A set of related classes to help kickstart the development of a plugin for WooCommerce.
 * Version:                 1.0.0
 * Requires at least:       5.5
 * Requires PHP:            7.4
 * Author:                  Deep Web Solutions GmbH
 * Author URI:              https://www.deep-web-solutions.com
 * License:                 GPL-3.0+
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:             dws-wp-framework-woocommerce
 * Domain Path:             /src/languages
 * WC requires at least:    4.5
 * WC tested up to:         5.0
 */

namespace DeepWebSolutions\Framework;

if ( ! \defined( 'ABSPATH' ) ) {
	return; // Since this file is autoloaded by Composer, 'exit' breaks all external dev tools.
}

// Start by autoloading dependencies and defining a few functions for running the bootstrapper.
// The conditional check makes the whole thing compatible with Composer-based WP management.
\is_file( __DIR__ . '/vendor/autoload.php' ) && require_once __DIR__ . '/vendor/autoload.php';

// Load module-specific bootstrapping functions.
require_once __DIR__ . '/bootstrap-functions.php';

// Define settings constants
\define( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_NAME', dws_wp_framework_get_whitelabel_name() . ': Framework WooCommerce' );
\define( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_VERSION', '1.0.0' );
\define( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_BASE_PATH', __DIR__ );

// Define minimum environment requirements.
\define( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_MIN_PHP', '7.4' );
\define( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_MIN_WP', '5.5' );

// Bootstrap the settings (maybe)!
if ( dws_wp_framework_check_php_wp_requirements_met( dws_wp_framework_get_woocommerce_min_php(), dws_wp_framework_get_woocommerce_min_wp() ) ) {
	$dws_woocommerce_init_function = function() {
		\define(
			__NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_INIT',
			\apply_filters(
				'dws_wp_framework/woocommerce/init_status',
				dws_wp_framework_get_settings_init_status(),
				__NAMESPACE__
			)
		);
	};

	if ( \did_action( 'plugins_loaded' ) ) {
		\call_user_func( $dws_woocommerce_init_function );
	} else {
		\add_action( 'plugins_loaded', $dws_woocommerce_init_function, PHP_INT_MIN + 600 );
	}
} else {
	\define( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_INIT', false );
	dws_wp_framework_output_requirements_error( dws_wp_framework_get_woocommerce_name(), dws_wp_framework_get_woocommerce_version(), dws_wp_framework_get_woocommerce_min_php(), dws_wp_framework_get_woocommerce_min_wp() );

	// Stop the foundations from initializing if the WooCommerce module failed.
	\add_filter(
		'dws_wp_framework/foundations/init_status',
		function( bool $init, string $namespace ) {
			return ( __NAMESPACE__ === $namespace ) ? false : $init;
		},
		10,
		2
	);
}
