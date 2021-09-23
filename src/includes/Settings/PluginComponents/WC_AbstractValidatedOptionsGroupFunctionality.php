<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\PluginComponents;

use DeepWebSolutions\Framework\Settings\PluginComponents\AbstractOptionsPageFunctionality;
use DeepWebSolutions\Framework\Settings\PluginComponents\AbstractValidatedOptionsGroupFunctionality;
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
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings\PluginComponents
 */
abstract class WC_AbstractValidatedOptionsGroupFunctionality extends AbstractValidatedOptionsGroupFunctionality {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 */
	public function get_parent(): ?WC_AbstractValidatedOptionsSectionFunctionality {
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->get_parent_node_trait();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_option_value( string $field_id ) {
		return $this->get_option_value_trait( $field_id, $this->get_group_id(), array( 'default' => null ), 'woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function update_option_value( string $field_id, $value, ?bool $autoload = null ) {
		return $this->update_option_value_trait( $field_id, $value, $this->get_group_id(), array( 'autoload' => $autoload ), 'woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete_option_value( string $field_id ) {
		return $this->get_settings_service()->delete_option_value( $field_id, $this->get_group_id(), array(), 'woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		parent::register_hooks( $hooks_service );

		$hooks_service->add_filter( $this->get_hook_tag( 'get_group_fields' ), $this, 'handle_conditional_logic', 999, 1, 'internal' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	protected function register_options_group( SettingsService $settings_service, AbstractOptionsPageFunctionality $options_section ) {
		$settings_service->register_options_group(
			$this->get_group_id(),
			array( $this, 'get_group_title' ),
			array( null, fn() => \apply_filters( $this->get_hook_tag( 'get_group_fields' ), $this->get_group_fields() ) ),
			$options_section->get_section_parent_slug(),
			array( 'section' => $options_section->get_page_slug() ),
			'woocommerce'
		);
	}

	// endregion

	// region METHODS

	/**
	 * Returns the options fields' definition.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array[]
	 */
	abstract public function get_group_fields(): array;

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
	 * @return  array
	 */
	public function handle_conditional_logic( array $options ): array {
		return $options;
	}

	// endregion
}
