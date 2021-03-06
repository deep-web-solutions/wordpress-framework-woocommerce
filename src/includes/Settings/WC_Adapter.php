<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotSupportedException;
use DeepWebSolutions\Framework\Helpers\WordPress\Users;
use DeepWebSolutions\Framework\Settings\SettingsAdapterInterface;
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
class WC_Adapter implements SettingsAdapterInterface {
	// region CREATE

	/**
	 * Registers a new WooCommerce settings page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
			add_filter(
				'woocommerce_get_settings_pages',
				function( $settings ) use ( $promise, $menu_slug, $menu_title ) {
					$settings_page = new WC_Settings_Page( $menu_slug, $menu_title );

					$settings[] = $settings_page;
					$promise->resolve( $settings_page );
					Utils::queue()->run();

					return $settings;
				}
			);
		}

		return $promise;
	}

	/**
	 * Registers a new WooCommerce settings section within a tab.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
			add_filter(
				'woocommerce_get_sections_' . $parent_slug,
				function( $sections ) use ( $menu_slug, $menu_title ) {
					return $sections + array( $menu_slug => $menu_title );
				}
			);

			return $menu_slug;
		}

		return null;
	}

	/**
	 * Registers a group of settings to be outputted on a WooCommerce settings tab.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $group_id       The ID of the settings group.
	 * @param   string  $group_title    The title of the settings group.
	 * @param   array   $fields         The fields to be registered with the group.
	 * @param   string  $page           The settings page on which the group's fields should be displayed.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @return  bool
	 */
	public function register_options_group( string $group_id, string $group_title, array $fields, string $page, array $params ): bool {
		if ( ! did_action( 'woocommerce_sections_' . $page ) ) {
			return add_filter(
				'woocommerce_get_settings_' . $page,
				function( $settings ) use ( $group_id, $group_title, $fields, $params ) {
					global $current_section;

					if ( ( $params['section'] ?? '' ) === $current_section ) {
						if ( is_callable( $fields ) ) {
							$fields = call_user_func_array( $fields, $params['args'] ?? array() );
						}

						$fields = array_map(
							function( array $field ) use ( $group_id ) {
								$field['id'] = "{$group_id}_{$field['id']}";
							},
							$fields
						);

						$settings += array(
							"{$group_id}_start" => array(
								'name' => $group_title,
								'type' => 'title',
								'desc' => $params['desc'] ?? '',
								'id'   => "{$group_id}_start",
							),
						) + $fields + array(
							"{$group_id}_end" => array(
								'type' => 'sectionend',
								'id'   => "{$group_id}_end",
							),
						);
					}

					return $settings;
				},
				10
			);
		}

		return false;
	}

	/**
	 * Registers a group of settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $group_id       The ID of the settings group.
	 * @param   string  $group_title    The title of the settings group.
	 * @param   array   $fields         The fields to be registered with the group.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 *
	 * @return  void
	 */
	public function register_generic_group( string $group_id, string $group_title, array $fields, array $params ): void {
		throw new NotSupportedException();
	}

	/**
	 * Registers a custom field dynamically at a later point than the parent group's creation.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $group_id       The ID of the parent group that the dynamically added field belongs to.
	 * @param   string  $field_id       The ID of the newly registered field.
	 * @param   string  $field_title    The title of the newly registered field.
	 * @param   string  $field_type     The type of custom field being registered.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 *
	 * @return  void
	 */
	public function register_field( string $group_id, string $field_id, string $field_title, string $field_type, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion

	// region READ

	/**
	 * Reads a setting's value from the database.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $field_id       The ID of the field within the settings to read from the database.
	 * @param   string  $settings_id    The ID of the settings group.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @return  mixed
	 */
	public function get_option_value( string $field_id, string $settings_id, array $params = array() ) {
		return get_option( "{$settings_id}_{$field_id}", $params['default'] ?? false );
	}

	/**
	 * Reads a field's value from the database.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $field_id       The ID of the field to read from the database.
	 * @param   mixed   $object_id      The ID of the object the data is for.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 *
	 * @return  void
	 */
	public function get_field_value( string $field_id, $object_id, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion

	// region UPDATE

	/**
	 * Updates a setting's value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $field_id       The ID of the field within the settings to update.
	 * @param   mixed   $value          The new value of the setting.
	 * @param   string  $settings_id    The ID of the settings group.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @return  bool
	 */
	public function update_option_value( string $field_id, $value, string $settings_id, array $params = array() ): bool {
		return update_option( "{$settings_id}_{$field_id}", $value, $params['autoload'] ?? null );
	}

	/**
	 * Updates a field's value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $field_id       The ID of the field to update.
	 * @param   mixed   $value          The new value of the setting.
	 * @param   mixed   $object_id      The ID of the object the update is for.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 *
	 * @return  void
	 */
	public function update_field_value( string $field_id, $value, $object_id, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion

	// region DELETE

	/**
	 * Deletes a setting from the database.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $field_id       The ID of the settings field to remove from the database. Empty string to delete the whole group.
	 * @param   string  $settings_id    The ID of the settings group.
	 * @param   array   $params         Other parameters required for the adapter to work.
	 *
	 * @return  bool
	 */
	public function delete_option( string $field_id, string $settings_id, array $params = array() ): bool {
		return delete_option( "{$settings_id}_{$field_id}" );
	}

	/**
	 * Deletes a field's value from the database.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $field_id   The ID of the field to delete from the database.
	 * @param   mixed   $object_id  The ID of the object the deletion is for.
	 * @param   array   $params     Other parameters required for the adapter to work.
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 *
	 * @return  void
	 */
	public function delete_field( string $field_id, $object_id, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion
}
