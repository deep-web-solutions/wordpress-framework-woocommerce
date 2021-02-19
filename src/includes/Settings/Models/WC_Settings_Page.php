<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\Models;

defined( 'ABSPATH' ) || exit;

/**
 * A generic model for a WC settings page so that we don't have to reinvent the wheel but simply use the classes already
 * offered by WC.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings\Models
 */
class WC_Settings_Page extends \WC_Settings_Page {
	// region MAGIC METHODS

	/**
	 * WC_Settings_Page constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $id     ID of the settings page.
	 * @param   string  $label  Title of the settings page.
	 */
	public function __construct( string $id, string $label ) {
		$this->id    = $id;
		$this->label = $label;
		parent::__construct();
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Get settings array.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_settings(): array {
		return apply_filters( 'woocommerce_get_settings_' . $this->id, array(), $GLOBALS['current_section'] );
	}

	// endregion
}
