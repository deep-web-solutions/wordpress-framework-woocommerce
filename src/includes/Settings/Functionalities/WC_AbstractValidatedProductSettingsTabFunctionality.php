<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities;

use DeepWebSolutions\Framework\Core\AbstractPluginFunctionality;
use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Utilities\Hooks\Actions\SetupHooksTrait;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Validation\Actions\InitializeValidationServiceTrait;
use DeepWebSolutions\Framework\Utilities\Validation\ValidationServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Validation\ValidationServiceAwareTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Template for creating a new WC product settings tab.
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings\Functionalities
 */
abstract class WC_AbstractValidatedProductSettingsTabFunctionality extends AbstractPluginFunctionality implements ValidationServiceAwareInterface {
	// region TRAITS

	use InitializeValidationServiceTrait , ValidationServiceAwareTrait {
		ValidationServiceAwareTrait::get_default_value as protected get_default_value_trait;
		ValidationServiceAwareTrait::get_supported_options as protected get_supported_options_trait;
		ValidationServiceAwareTrait::validate_value as protected validate_value_trait;
		ValidationServiceAwareTrait::validate_allowed_value as protected validate_allowed_value_trait;
	}
	use SetupHooksTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		$hooks_service->add_action( 'admin_print_scripts-post.php', $this, 'enqueue_scripts' );
		$hooks_service->add_action( 'admin_print_scripts-post-new.php', $this, 'enqueue_scripts' );

		$hooks_service->add_filter( 'woocommerce_product_data_tabs', $this, 'register_settings_tab' );
		$hooks_service->add_action( 'woocommerce_product_data_panels', $this, 'output_settings_panel' );
		$hooks_service->add_action( 'woocommerce_process_product_meta', $this, 'save_settings' );

		$hooks_service->add_filter( 'default_post_metadata', $this, 'filter_default_metadata', 99, 3 );
		$hooks_service->add_filter( 'woocommerce_data_store_wp_post_read_meta', $this, 'filter_default_wc_metadata', 99, 2 );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function get_default_value( string $field_id ) {
		return $this->get_default_value_trait( $field_id, $this->get_validation_handler_id() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function get_supported_options( string $field_id ) {
		return $this->get_supported_options_trait( $field_id, $this->get_validation_handler_id() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	protected function validate_value( $value, string $field_id, string $validation_type ) {
		return $this->validate_value_trait( $value, $field_id, $validation_type, $this->get_validation_handler_id() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	protected function validate_allowed_value( $value, string $field_id, string $options_key, string $validation_type ) {
		return $this->validate_allowed_value_trait( $value, $field_id, $options_key, $validation_type, $this->get_validation_handler_id() );
	}

	// endregion

	// region TAB METHODS

	/**
	 * Returns the slug of the new product settings tab.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	abstract public function get_tab_slug(): string;

	/**
	 * Returns the title of the new product settings tab.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	abstract public function get_tab_title(): string;

	/**
	 * Returns any additional CSS classes to register on the product settings tab.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_tab_classes(): array {
		return array();
	}

	// endregion

	// region VALIDATION METHODS

	/**
	 * Returns the ID of the validation handler to use.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_validation_handler_id(): string {
		return 'product-settings';
	}

	/**
	 * Returns a map from the global config settings' names to their product config settings' names.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_global_to_product_config_map(): array {
		$map = $this->get_global_to_product_config_map_helper();
		return \apply_filters( $this->get_hook_tag( 'global_to_product_config_map' ), $map );
	}

	/**
	 * Returns a map from the config settings' names to their product-meta names.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_config_to_meta_map(): array {
		$map = $this->get_config_to_meta_map_helper();
		return \apply_filters( $this->get_hook_tag( 'config_to_meta_map' ), $map );
	}

	/**
	 * Retrieves the value of a field from a given product.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id       The ID of the field to retrieve.
	 * @param   int     $product_id     The ID of the product to retrieve it from.
	 *
	 * @return  null|string|array
	 */
	public function get_field_value( string $field_id, int $product_id ) {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return null;
		}

		$product  = \wc_get_product( $product_id );
		$field_id = $this->maybe_map_config_to_meta( $field_id );

		return $product->get_meta( $field_id, true );
	}

	/**
	 * Retrieves the value of a field from a given product and validates it.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id       The ID of the field to retrieve.
	 * @param   int     $product_id     The ID of the product to retrieve it from.
	 *
	 * @return  null|string|array
	 */
	public function get_validated_field_value( string $field_id, int $product_id ) {
		$value = $this->get_field_value( $field_id, $product_id );
		$value = $this->validate_field_value( $value, $field_id );

		return \apply_filters( $this->get_hook_tag( 'get_validated_field_value' ), $value, $field_id );
	}

	/**
	 * Validates a given value assuming it belongs to a given field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @param   mixed   $value      The value to validate.
	 * @param   string  $field_id   The ID of the field the value is supposed to belong to.
	 *
	 * @return  mixed|null
	 */
	public function validate_field_value( $value, string $field_id ) {
		$validated_value = $this->validate_field_value_helper( $value, $field_id );
		return \apply_filters( $this->get_hook_tag( 'validate_field_value' ), $validated_value ?? null, $field_id, $value );
	}

	// endregion

	// region HOOKS

	/**
	 * Enqueues the conditional logic script.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function enqueue_scripts() {
		if ( false === \in_array( \get_current_screen()->id, array( 'product', 'edit-product' ), true ) ) {
			return;
		}

		$this->enqueue_scripts_helper();
	}

	/**
	 * Registers the new settings tab for WC products.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $tabs   The tabs currently registered.
	 *
	 * @return  array
	 */
	public function register_settings_tab( array $tabs ): array {
		$tabs[ $this->get_tab_slug() ] = array(
			'label'    => $this->get_tab_title(),
			'target'   => "{$this->get_tab_slug()}_data",
			'class'    => \array_merge(
				array( "{$this->get_tab_slug()}_tab" ),
				$this->get_tab_classes()
			),
			'priority' => 65,
		);

		return $tabs;
	}

	/**
	 * Outputs the product-level settings fields.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @retrun  void
	 */
	public function output_settings_panel(): void {
		global $product_object;

		if ( $product_object instanceof \WC_Product && true === $this->is_supported_product( $product_object->get_id() ) ) : ?>

		<div id="<?php echo \esc_attr( "{$this->get_tab_slug()}_data" ); ?>" class="panel woocommerce_options_panel">
			<?php \do_action( $this->get_hook_tag( 'fields', 'before_options_groups' ) ); ?>

			<?php $this->output_settings_panel_helper(); ?>

			<?php \do_action( $this->get_hook_tag( 'fields', 'after_options_groups' ) ); ?>
		</div>

			<?php
		endif;
	}

	/**
	 * Saves the tab's fields to the database.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $product_id     The ID of the product being saved.
	 *
	 * @return  void
	 */
	public function save_settings( int $product_id ): void {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return;
		}

		$this->save_settings_helper( $product_id );
	}

	/**
	 * Sets the default value for the new product fields. This is required, e.g., when editing a product that hasn't
	 * been saved since the plugin was installed otherwise all the fields would just appear empty.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value      The default value so far.
	 * @param   int     $object_id  The ID of the object being queried.
	 * @param   string  $meta_key   The meta key being queried.
	 *
	 * @return  InexistentPropertyException|mixed
	 */
	public function filter_default_metadata( $value, int $object_id, string $meta_key ) {
		if ( true === $this->is_supported_product( $object_id ) ) {
			$meta_to_setting_map = \array_flip( $this->get_config_to_meta_map() );
			if ( isset( $meta_to_setting_map[ $meta_key ] ) ) {
				$value = $this->get_default_value( $meta_to_setting_map[ $meta_key ] );
			}
		}

		return $value;
	}

	/**
	 * Sets the default value for the new product fields when they're being queried through the WC data store. This is required
	 * when interacting with the WC_Product object before the product post is saved after installing the plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array       $meta_data      All the meta data read.
	 * @param   object      $object         The object that the meta data was read for.
	 *
	 * @return  array
	 */
	public function filter_default_wc_metadata( array $meta_data, object $object ): array {
		if ( \is_a( $object, \WC_Product::class ) && true === $this->is_supported_product( $object->get_id() ) ) {
			$existing_meta_keys = \array_flip( \array_column( $meta_data, 'meta_key' ) );

			$meta_to_setting_map = \array_flip( $this->get_config_to_meta_map() );
			foreach ( $meta_to_setting_map as $meta_key => $setting_id ) {
				if ( ! isset( $existing_meta_keys[ $meta_key ] ) ) {
					// phpcs:disable WordPress.DB.SlowDBQuery
					$meta_data[] = (object) array(
						'meta_id'    => 0,
						'meta_key'   => $meta_key,
						'meta_value' => $this->get_default_value( $setting_id ),
					);
					// phpcs:enable WordPress.DB.SlowDBQuery
				}
			}
		}

		return $meta_data;
	}

	// endregion

	// region HELPERS

	/**
	 * Children classes can enqueue their scripts here knowing they will be outputted only on the appropriate screens.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	protected function enqueue_scripts_helper() {
		/* empty on purpose */
	}

	/**
	 * For a given product setting field, returns its meta name, if known.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id   Product setting field ID.
	 *
	 * @return  string
	 */
	protected function maybe_map_config_to_meta( string $field_id ): string {
		$setting_to_meta_map = $this->get_config_to_meta_map();
		return $setting_to_meta_map[ $field_id ] ?? $field_id;
	}

	/**
	 * Children classes should return their mapping from global-level settings to this tab's settings in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	abstract protected function get_global_to_product_config_map_helper(): array;

	/**
	 * Children classes should return their mapping from config settings' names to their product-meta names in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	abstract protected function get_config_to_meta_map_helper(): array;

	/**
	 * Children classes can override this helper to restrict the tab only to selected products.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $product_id     The ID of the product to check support for.
	 *
	 * @return  bool|null
	 */
	protected function is_supported_product( int $product_id ): ?bool {
		$product = \wc_get_product( $product_id );
		if ( empty( $product ) ) {
			return null;
		}

		return true;
	}

	/**
	 * Children classes should output their settings fields in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  void
	 */
	abstract protected function output_settings_panel_helper(): void;

	/**
	 * Children classes should save their settings fields in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $product_id     The ID of the product being saved.
	 *
	 * @return  void
	 */
	abstract protected function save_settings_helper( int $product_id ): void;

	/**
	 * Children classes should define the validation logic for their fields in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value      Value to validate.
	 * @param   string  $field_id   The ID of the field that the value belongs to.
	 *
	 * @return  mixed
	 */
	abstract protected function validate_field_value_helper( $value, string $field_id );

	// endregion
}
