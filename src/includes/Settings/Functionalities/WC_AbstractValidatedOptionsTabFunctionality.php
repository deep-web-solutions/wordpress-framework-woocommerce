<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities;

use DeepWebSolutions\Framework\Settings\Functionalities\AbstractValidatedOptionsPageFunctionality;
use DeepWebSolutions\Framework\Settings\SettingsService;

\defined( 'ABSPATH' ) || exit;

/**
 * Template for creating a new WC settings tab.
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings\Functionalities
 */
abstract class WC_AbstractValidatedOptionsTabFunctionality extends AbstractValidatedOptionsPageFunctionality {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function register_options_page( SettingsService $settings_service ) {
		$settings_service->get_handler( 'woocommerce' )->register_menu_page(
			'',
			array( $this, 'get_page_title' ),
			$this->get_page_slug()
		);
	}

	// endregion
}
