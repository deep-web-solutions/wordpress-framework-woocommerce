<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings;

\defined( 'ABSPATH' ) || exit;

/**
 * A generic model for a WC settings page so that we don't have to reinvent the wheel but simply use the classes already
 * offered by WC.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings
 */
class WC_SettingsPage extends \WC_Settings_Page {
	// region MAGIC METHODS

	/**
	 * WC_Settings_Page constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $settings_tab_id        ID of the settings tab.
	 * @param   string  $settings_tab_label     Title of the settings tab.
	 */
	public function __construct( string $settings_tab_id, string $settings_tab_label ) {
		$this->id    = $settings_tab_id;
		$this->label = $settings_tab_label;
		parent::__construct();
	}

	// endregion
}
