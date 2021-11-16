<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities;

use DeepWebSolutions\Framework\Settings\Functionalities\AbstractOptionsPageFunctionality;
use DeepWebSolutions\Framework\Settings\Functionalities\AbstractValidatedOptionsGroupFunctionality;
use DeepWebSolutions\Framework\Settings\SettingsService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;

\defined( 'ABSPATH' ) || exit;

/**
 * Template for creating a new WC settings group.
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings\Functionalities
 */
abstract class WC_AbstractValidatedOptionsGroupFunctionality extends AbstractValidatedOptionsGroupFunctionality {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_option_value( string $field_id ) {
		return $this->get_option_value_trait( $field_id, $this->get_group_id(), array( 'default' => null ), 'woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function update_option_value( string $field_id, $value, ?bool $autoload = null ) {
		return $this->update_option_value_trait( $field_id, $value, $this->get_group_id(), array( 'autoload' => $autoload ), 'woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function delete_option_value( string $field_id ) {
		return $this->get_settings_service()->delete_option_value( $field_id, $this->get_group_id(), array(), 'woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		parent::register_hooks( $hooks_service );

		$hooks_service->add_filter( $this->get_hook_tag( 'get_group_fields' ), $this, 'handle_conditional_logic', 999, 1, 'direct' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	protected function register_options_group( SettingsService $settings_service, AbstractOptionsPageFunctionality $options_section ) {
		if ( $options_section instanceof WC_AbstractValidatedOptionsTabFunctionality ) {
			$settings_service->get_handler( 'woocommerce' )->register_submenu_page(
				$this->get_tab_slug( $options_section ),
				'',
				array( $this, 'get_group_title' ),
				$this->get_section_slug( $options_section )
			);
		}

		$settings_service->register_options_group(
			$this->get_group_id(),
			array( $this, 'get_group_title' ),
			array( $this, 'get_group_fields' ),
			$this->get_tab_slug( $options_section ),
			array( 'section' => $this->get_section_slug( $options_section ) ),
			'woocommerce'
		);
	}

	// endregion

	// region METHODS

	/**
	 * Returns the WC tab slug to output the group on.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AbstractOptionsPageFunctionality    $options_section    Either a WC options tab or section object.
	 *
	 * @return  string
	 */
	public function get_tab_slug( AbstractOptionsPageFunctionality $options_section ): string {
		return ( $options_section instanceof WC_AbstractValidatedOptionsSectionFunctionality )
			? $options_section->get_tab_slug() : $options_section->get_page_slug();
	}

	/**
	 * Returns the WC section slug to output the group on.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AbstractOptionsPageFunctionality    $options_section    Either a WC options tab or section object.
	 *
	 * @return  string
	 */
	public function get_section_slug( AbstractOptionsPageFunctionality $options_section ): string {
		return ( $options_section instanceof WC_AbstractValidatedOptionsSectionFunctionality )
			? $options_section->get_page_slug() : '';
	}

	// endregion

	// region HOOKS

	/**
	 * Hook for consolidating all conditional logic related to fields registration.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $options    Registered options fields.
	 *
	 * @return  array[]
	 */
	public function handle_conditional_logic( array $options ): array {
		return $options;
	}

	// endregion
}
