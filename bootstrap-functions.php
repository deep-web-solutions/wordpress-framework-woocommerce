<?php
/**
 * Defines module-specific getters and functions.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce
 *
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace DeepWebSolutions\Framework;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns the whitelabel name of the framework's WooCommerce layer within the context of the current plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function dws_wp_framework_get_woocommerce_name() {
	return \constant( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_NAME' );
}

/**
 * Returns the version of the framework's WooCommerce layer within the context of the current plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function dws_wp_framework_get_woocommerce_version() {
	return \constant( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_VERSION' );
}

/**
 * Returns the minimum PHP version required to run the Bootstrapper of the framework's settings within the context of the current plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function dws_wp_framework_get_woocommerce_min_php() {
	return \constant( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_MIN_PHP' );
}

/**
 * Returns the minimum WP version required to run the Bootstrapper of the framework's settings within the context of the current plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function dws_wp_framework_get_woocommerce_min_wp() {
	return \constant( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_MIN_WP' );
}

/**
 * Returns whether the WooCommerce package has managed to initialize successfully or not in the current environment.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  bool
 */
function dws_wp_framework_get_woocommerce_init_status() {
	return \defined( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_INIT' ) && \constant( __NAMESPACE__ . '\DWS_WP_FRAMEWORK_WOOCOMMERCE_INIT' );
}
