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

	public function __construct( string $id, string $label ) {
		$this->id = $id;
		$this->label = $label;
		parent::__construct();
	}

	// endregion
}