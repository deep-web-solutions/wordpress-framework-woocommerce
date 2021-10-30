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
class WC_Handler extends AbstractSettingsHandler {
	// region MAGIC METHODS

	/**
	 * WooCommerce Settings Handler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string              $handler_id     The ID of the settings handler.
	 * @param   WC_Adapter|null     $adapter        Instance of the adapter to the WC settings framework.
	 */
	public function __construct( string $handler_id = 'woocommerce', ?WC_Adapter $adapter = null ) {
		parent::__construct( $handler_id, $adapter ?? new WC_Adapter() );
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

	// endregion
}
