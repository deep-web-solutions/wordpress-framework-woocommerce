<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotSupportedException;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Helpers\DataTypes\Callables;
use DeepWebSolutions\Framework\Helpers\DataTypes\Strings;
use DeepWebSolutions\Framework\Helpers\Users;
use DeepWebSolutions\Framework\Settings\SettingsAdapterInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Interacts with the Settings API of the WooCommerce plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings
 */
class WC_SettingsAdapter implements SettingsAdapterInterface {
	// region CREATE

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function register_menu_page( $unused, $menu_title, string $menu_slug, string $capability = 'manage_woocommerce', array $params = array() ): bool {
		if ( ! Users::has_capabilities( $capability ) ) {
			return false;
		}

		return \add_filter(
			'woocommerce_get_settings_pages',
			function( $settings ) use ( $menu_slug, $menu_title ) {
				$settings[] = new WC_SettingsPage( $menu_slug, Strings::resolve( $menu_title ) );
				return $settings;
			}
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function register_submenu_page( string $parent_slug, $unused, $menu_title, string $menu_slug, string $capability = 'manage_woocommerce', array $params = array() ): bool {
		if ( ! Users::has_capabilities( $capability ) || \did_action( "woocommerce_sections_$parent_slug" ) ) {
			return false;
		}

		return \add_filter(
			"woocommerce_get_sections_$parent_slug",
			fn( $sections ) => $sections + array( $menu_slug => Strings::resolve( $menu_title ) ),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function register_options_group( string $group_id, $group_title, $fields, string $page, array $params ): bool {
		if ( \did_action( "woocommerce_sections_$page" ) ) {
			return false;
		}

		return \add_filter(
			"woocommerce_get_settings_$page",
			function( array $settings, ?string $section_id = null ) use ( $group_id, $group_title, $fields, $params ) {
				$section_id = $section_id ?? $GLOBALS['current_section'] ?? null;
				if ( ( $params['section'] ?? '' ) !== $section_id ) {
					return $settings;
				}

				$fields = Arrays::validate( Callables::maybe_resolve( $fields, $params['args'] ?? array() ), array() );

				if ( ! empty( $fields ) ) {
					\array_walk(
						$fields,
						function( &$field, $key ) use ( $group_id ) {
							$field['id'] = "{$group_id}_$key";
						}
					);

					$settings += array(
						"{$group_id}_start" => array(
							'name' => Strings::resolve( $group_title ),
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
			10,
			2
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 */
	public function register_generic_group( string $group_id, $group_title, $fields, array $locations, array $params ) {
		throw new NotSupportedException();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 */
	public function register_field( string $group_id, string $field_id, $field_title, string $field_type, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion

	// region READ

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_option_value( string $field_id, string $settings_id, array $params = array() ) {
		$params = \wp_parse_args( $params, array( 'default' => false ) );
		return \get_option( "{$settings_id}_$field_id", $params['default'] );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 */
	public function get_field_value( string $field_id, $object_id, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion

	// region UPDATE

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function update_option_value( string $field_id, $value, string $settings_id, array $params = array() ): bool {
		return \update_option( "{$settings_id}_$field_id", $value, $params['autoload'] ?? null );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 */
	public function update_field_value( string $field_id, $value, $object_id, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion

	// region DELETE

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function delete_option_value( string $field_id, string $settings_id, array $unused = array() ): bool {
		return \delete_option( "{$settings_id}_$field_id" );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @throws  NotSupportedException   Adapter does not support this method currently.
	 */
	public function delete_field_value( string $field_id, $object_id, array $params ): void {
		throw new NotSupportedException();
	}

	// endregion
}
