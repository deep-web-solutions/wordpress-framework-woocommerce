<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings;

use DeepWebSolutions\Framework\Settings\AbstractSettingsHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Interacts with the Settings API of the WooCommerce plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings
 */
class WC_SettingsHandler extends AbstractSettingsHandler {
	// region MAGIC METHODS

	/**
	 * WooCommerce Settings Handler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string                      $handler_id     The ID of the settings handler.
	 * @param   WC_SettingsAdapter|null     $adapter        Instance of the adapter to the WC settings framework.
	 */
	public function __construct( string $handler_id = 'woocommerce', ?WC_SettingsAdapter $adapter = null ) {
		parent::__construct( $handler_id, $adapter ?? new WC_SettingsAdapter() );
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_action_hook( string $context ): string {
		switch ( $context ) {
			default:
				return 'woocommerce_loaded';
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function register_menu_page( $unused, $menu_title, string $menu_slug, string $capability = 'manage_woocommerce', array $params = array() ) { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		return parent::register_menu_page( $unused, $menu_title, $menu_slug, $capability, $params );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function register_submenu_page( string $parent_slug, $unused, $menu_title, string $menu_slug, string $capability = 'manage_woocommerce', array $params = array() ): bool { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		return parent::register_submenu_page( $parent_slug, $unused, $menu_title, $menu_slug, $capability, $params );
	}

	// endregion
}
