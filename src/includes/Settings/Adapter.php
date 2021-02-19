<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings;

use DeepWebSolutions\Framework\Helpers\WordPress\Users;
use DeepWebSolutions\Framework\Settings\Interfaces\Actions\Adapterable;
use DeepWebSolutions\Framework\WooCommerce\Settings\Models\WC_Settings_Page;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;

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

	/**
	 * Registers a new WooCommerce settings page.
	 *
	 * @param   string  $page_title     NOT USED BY THIS ADAPTER.
	 * @param   string  $menu_title     The text to be used for the WC settings tab.
	 * @param   string  $menu_slug      The slug name to refer to this tab by. Should be unique for this tab and only
	 *                                  include lowercase alphanumeric, dashes, and underscores characters to be compatible
	 *                                  with sanitize_key().
	 * @param   string  $capability     The capability required for this menu to be displayed to the user.
	 * @param   array   $params         Other params required for the adapter to work.
	 *
	 * @return  Promise
	 */
	public function register_menu_page( string $page_title, string $menu_title, string $menu_slug, string $capability = 'manage_woocommerce', array $params = array() ): Promise {
		$promise = new Promise();

		if ( Users::has_capabilities( array( $capability ) ) ) {
			add_filter( 'woocommerce_get_settings_pages', function( $settings ) use ( $promise, $menu_slug, $menu_title ) {
				$settings_page = new WC_Settings_Page( $menu_slug, $menu_title );

				$settings[] = $settings_page;
				$promise->resolve( $settings_page );
				Utils::queue()->run();

				return $settings;
			} );
		}

		return $promise;
	}

	/**
	 * Registers a new WooCommerce settings section within a tab.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $parent_slug    The slug name for the parent WC tab.
	 * @param   string  $page_title     NOT USED BY THIS ADAPTER.
	 * @param   string  $menu_title     The text to be used for the section.
	 * @param   string  $menu_slug      The slug name to refer to this section by. Should be unique for this menu page and only
	 *                                  include lowercase alphanumeric, dashes, and underscores characters to be compatible
	 *                                  with sanitize_key().
	 * @param   string  $capability     The capability required for this menu to be displayed to the user.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @return  string|null
	 */
	public function register_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $menu_slug, string $capability = 'manage_woocommerce', array $params = array() ): ?string {
		if ( Users::has_capabilities( array( $capability ) ) && ! did_action( 'woocommerce_sections_' . $parent_slug ) ) {
			add_filter( 'woocommerce_get_sections_' . $parent_slug, function( $sections ) use ( $menu_slug, $menu_title ) {
				return $sections + array( $menu_slug => $menu_title );
			} );

			return $menu_slug;
		}

		return null;
	}

	public function register_settings_group( string $group_id, string $group_title, array $fields, string $page, array $params ): bool {
		if ( ! did_action( 'woocommerce_sections_' . $page ) ) {
			return add_filter( 'woocommerce_get_settings_' . $page, function( $settings ) use ( $group_id, $group_title, $fields, $params ) {
				$settings += array(
					"{$group_id}_start" => array(
						'name' => $group_title,
						'type' => 'title',
						'desc' => $params['desc'] ?? '',
						'id'   => "{$group_id}_start",
					)
				) + $fields + array(
						"{$group_id}_end" => array(
						'type' => 'sectionend',
						'id'   => "{$group_id}_end",
					)
				);

				return $settings;
			} );
		}

		return false;
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
