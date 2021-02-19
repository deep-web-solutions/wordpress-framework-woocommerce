<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings;

use DeepWebSolutions\Framework\Settings\Interfaces\Actions\Adapterable;
use DeepWebSolutions\Framework\WooCommerce\Settings\Models\WC_Settings_Page;

defined( 'ABSPATH' ) || exit;

/**
 * Interacts with the Settings API of the WooCommerce plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings
 */
class Adapter implements Adapterable {
	// region CREATE

	public function register_menu_page( string $page_title, string $menu_title, string $menu_slug, string $capability, array $params ) {
		$settings_page = new WC_Settings_Page( $menu_slug, $menu_title );

		add_filter( 'woocommerce_get_settings_pages', function( $settings ) use ( $settings_page ) {
			$settings[] = $settings_page;
			return $settings;
		} );
	}

	public function register_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $menu_slug, string $capability, array $params ) {
		// TODO: Implement register_submenu_page() method.
	}

	public function register_settings_group( string $group_id, string $group_title, array $fields, string $page, array $params ) {
		// TODO: Implement register_settings_group() method.
	}

	public function register_generic_group( string $group_id, string $group_title, array $fields, array $params ) {
		// TODO: Implement register_generic_group() method.
	}

	public function register_field( string $group_id, string $field_id, string $field_title, string $field_type, array $params ) {
		// TODO: Implement register_field() method.
	}

	// endregion

	// region READ

	public function get_setting_value( string $field_id, string $settings_id, array $params ) {
		// TODO: Implement get_setting_value() method.
	}

	public function get_field_value( string $field_id, $object_id, array $params = array() ) {
		// TODO: Implement get_field_value() method.
	}

	// endregion

	// region UPDATE

	public function update_settings_value( string $field_id, $value, string $settings_id, array $params ) {
		// TODO: Implement update_settings_value() method.
	}

	public function update_field_value( string $field_id, $value, $object_id, array $params ) {
		// TODO: Implement update_field_value() method.
	}

	// endregion

	// region DELETE

	public function delete_setting( string $field_id, string $settings_id, array $params ) {
		// TODO: Implement delete_setting() method.
	}

	public function delete_field( string $field_id, $object_id, array $params ) {
		// TODO: Implement delete_field() method.
	}

	// endregion
}
