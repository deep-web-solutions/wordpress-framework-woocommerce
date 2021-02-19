<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings;

use DeepWebSolutions\Framework\Settings\Abstracts\Handler as SettingsHandler;
use DeepWebSolutions\Framework\Utilities\Services\LoggingService;

defined( 'ABSPATH' ) || exit;

/**
 * Interacts with the Settings API of the WooCommerce plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings
 */
class Handler extends SettingsHandler {
	// region MAGIC METHODS

	/**
	 * WooCommerce Settings Handler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   Adapter         $adapter            Instance of the adapter to the WC settings framework.
	 * @param   LoggingService  $logging_service    Instance of the logging service.
	 */
	public function __construct( Adapter $adapter, LoggingService $logging_service ) { // phpcs:ignore
		parent::__construct( $adapter, $logging_service );
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the hook on which the WC framework is ready to be used.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @param   string  $context    The action being executed.
	 *
	 * @return  string
	 */
	public function get_action_hook( string $context ): string {
		return 'woocommerce_loaded';
	}

	/**
	 * Gets the instance of the settings framework adapter. Overwriting this method has no value other than helping
	 * with auto-complete in IDEs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  Adapter
	 */
	public function get_adapter(): Adapter { // phpcs:ignore
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::get_adapter();
	}

	// endregion
}
