<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Settings\Functionalities\AbstractValidatedOptionsPageFunctionality;
use DeepWebSolutions\Framework\Settings\SettingsService;

\defined( 'ABSPATH' ) || exit;

/**
 * Template for creating a new WC settings section.
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings\Functionalities
 */
abstract class WC_AbstractValidatedOptionsSectionFunctionality extends AbstractValidatedOptionsPageFunctionality {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function register_options_page( SettingsService $settings_service ) {
		$settings_service->get_handler( 'woocommerce' )->register_submenu_page(
			$this->get_tab_slug(),
			'',
			array( $this, 'get_page_title' ),
			$this->get_page_slug()
		);
	}

	// endregion

	// region METHODS

	/**
	 * Returns the section's parent, i.e. tab, slug.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotImplementedException     Thrown when not overridden and the parent is not a WC tab functionality.
	 *
	 * @return  string
	 */
	public function get_tab_slug(): string {
		$parent = $this->get_parent();
		if ( \is_a( $parent, WC_AbstractValidatedOptionsTabFunctionality::class ) ) {
			return $parent->get_page_slug();
		}

		throw new NotImplementedException( __FUNCTION__ . ' must be overridden in child class if parent is not of type ' . WC_AbstractValidatedOptionsTabFunctionality::class );
	}

	// endregion
}
