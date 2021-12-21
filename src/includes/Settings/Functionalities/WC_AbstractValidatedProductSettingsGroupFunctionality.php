<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities;

use DeepWebSolutions\Framework\Core\AbstractPluginFunctionality;
use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Helpers\DataTypes\Strings;
use DeepWebSolutions\Framework\Utilities\Hooks\Actions\SetupHooksTrait;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Validation\Actions\InitializeValidationServiceTrait;
use DeepWebSolutions\Framework\Utilities\Validation\ValidationServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Validation\ValidationServiceAwareTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Template for creating a new WC product settings group.
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings\Functionalities
 */
abstract class WC_AbstractValidatedProductSettingsGroupFunctionality extends AbstractPluginFunctionality implements ValidationServiceAwareInterface {
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
	public function get_parent(): ?WC_AbstractValidatedProductSettingsTabFunctionality {
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->parent;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		$hooks_service->add_filter( $this->get_parent()->get_hook_tag( 'get_field_value' ), $this, 'maybe_get_field_value', 10, 3, 'direct' );
		$hooks_service->add_filter( $this->get_parent()->get_hook_tag( 'get_validated_field_value' ), $this, 'maybe_get_validated_field_value', 10, 3, 'direct' );
		$hooks_service->add_filter( $this->get_parent()->get_hook_tag( 'update_field_value' ), $this, 'maybe_update_field_value', 10, 4, 'direct' );
		$hooks_service->add_filter( $this->get_parent()->get_hook_tag( 'delete_field_value' ), $this, 'maybe_delete_field_value', 10, 3, 'direct' );
		$hooks_service->add_filter( $this->get_parent()->get_hook_tag( 'validate_field_value' ), $this, 'maybe_validate_field_value', 10, 3, 'direct' );

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
	public function get_default_value( string $field_id, string $handler_id = 'product-settings' ) {
		return $this->get_default_value_trait( $this->generate_validation_key( $field_id ), $handler_id );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function get_supported_options( string $field_id, string $handler_id = 'product-settings' ) {
		return $this->get_supported_options_trait( $this->generate_validation_key( $field_id ), $handler_id );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	protected function validate_value( $value, string $field_id, string $validation_type, string $handler_id = 'product-settings' ) {
		return $this->validate_value_trait( $value, $this->generate_validation_key( $field_id ), $validation_type, $handler_id );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	protected function validate_allowed_value( $value, string $field_id, string $options_key, string $validation_type, string $handler_id = 'product-settings' ) {
		return $this->validate_allowed_value_trait( $value, $this->generate_validation_key( $field_id ), $this->generate_validation_key( $options_key ), $validation_type, $handler_id );
	}

	// endregion

	// region CRUD

	/**
	 * Returns the raw database value of a given field from a given product.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id       The ID of the field to retrieve the value for.
	 * @param   int     $product_id     The ID of the product to retrieve the value from.
	 *
	 * @return  null|array|string
	 */
	public function get_field_value( string $field_id, int $product_id ) {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return null;
		}

		$product = \wc_get_product( $product_id );
		return $product->get_meta( $this->generate_meta_key( $field_id ), true );
	}

	/**
	 * Returns the validated database value of a given field from a given product.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id       The ID of the field to retrieve the value for.
	 * @param   int     $product_id     The ID of the product to retrieve the value from.
	 *
	 * @return  mixed
	 */
	public function get_validated_field_value( string $field_id, int $product_id ) {
		$value = $this->get_field_value( $field_id, $product_id );
		if ( ! \is_null( $value ) ) { // null is only returned when the product ID is invalid.
			$value = $this->validate_field_value( $value, $field_id );
		}

		return \apply_filters( $this->get_hook_tag( 'get_validated_field_value' ), $value, $field_id );
	}

	/**
	 * Updates the raw database value of a given field for a given product.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $field_id       The ID of the field to update the value for.
	 * @param   int             $product_id     The ID of the product to update the value for.
	 * @param   string|array    $value          The new value.
	 *
	 * @return void
	 */
	public function update_field_value( string $field_id, int $product_id, $value ) {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return;
		}

		$product = \wc_get_product( $product_id );
		$product->update_meta_data( $this->generate_meta_key( $field_id ), $value );
		$product->save_meta_data();
	}

	/**
	 * Deletes the raw database value of a given field for a given product.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id       The ID of the field to delete the value for.
	 * @param   int     $product_id     The ID of the product to delete the value from.
	 *
	 * @return  void
	 */
	public function delete_field_value( string $field_id, int $product_id ) {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return;
		}

		$product = \wc_get_product( $product_id );
		$product->delete_meta_data( $this->generate_meta_key( $field_id ) );
		$product->save_meta_data();
	}

	/**
	 * Validates a given value assuming to belong to the given field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed       $value          The value to validate.
	 * @param   string      $field_id       The ID of the field to retrieve the value for.
	 * @param   int|null    $product_id     The ID of the product to validate the value for. Optional.
	 *
	 * @return  mixed
	 */
	public function validate_field_value( $value, string $field_id, ?int $product_id = null ) {
		$validated_value = $this->validate_field_value_helper( $value, $field_id, $product_id );
		return \apply_filters( $this->get_hook_tag( 'validate_field_value' ), $validated_value, $field_id, $value );
	}

	// endregion

	// region METHODS

	/**
	 * Children classes can override this helper to restrict the tab only to selected products. By default, follows the same
	 * rules as the tab component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $product_id     The ID of the product to check support for.
	 *
	 * @return  bool|null
	 */
	public function is_supported_product( int $product_id ): ?bool {
		$is_supported_product = $this->get_parent()->is_supported_product( $product_id );
		return \apply_filters( $this->get_hook_tag( 'is_supported_product' ), $is_supported_product, $product_id );
	}

	/**
	 * Returns the ID of the meta group. Needed for outputting the group itself and for performing CRUD operations
	 * on the fields later on.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_group_id(): string {
		$meta_prefix = $this->get_parent()->get_meta_key_prefix();
		$meta_group  = $this->get_group_name();

		return Strings::maybe_suffix( $meta_prefix, '_' ) . $meta_group;
	}

	/**
	 * Returns the name of the group for purposes of retrieving metadata.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_group_name(): string {
		return Strings::replace_placeholders(
			array(
				'_settings' => '',
				'_options'  => '',
				'_product'  => '',
				'_'         => '-',
			),
			self::get_safe_name()
		);
	}

	/**
	 * Returns any additional CSS classes to output on the product settings group.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_group_classes(): array {
		return array(
			Strings::to_safe_string(
				Strings::maybe_unprefix( "{$this->get_group_id()}_options_group" ),
				array( '-' => '_' )
			),
		);
	}

	/**
	 * Returns the group's fields definition.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array[]
	 */
	public function get_group_fields(): array {
		return \apply_filters( $this->get_hook_tag( 'get_group_fields' ), $this->get_group_fields_helper() );
	}

	/**
	 * Returns the composite key needed to query the database value of a given field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id   The ID of the field.
	 *
	 * @return  string
	 */
	public function generate_meta_key( string $field_id ): string {
		return Strings::maybe_prefix( $field_id, "{$this->get_group_id()}_" );
	}

	/**
	 * Given a field's meta key, returns the field ID.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $meta_key   The meta key.
	 *
	 * @return  string
	 */
	public function ungenerate_meta_key( string $meta_key ): string {
		return Strings::maybe_unprefix( $meta_key, "{$this->get_group_id()}_" );
	}

	/**
	 * Returns the composite key needed to pass on to the validation service in order to find the entries pertaining
	 * to a given field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id   The ID of the options field.
	 *
	 * @return  string
	 */
	public function generate_validation_key( string $field_id ): string {
		return "{$this->get_group_name()}/$field_id";
	}

	/**
	 * Children classes should define their field-saving logic in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $product_id     The ID of the product to save the metadata to.
	 *
	 * @return  void
	 */
	abstract public function save_group_fields( int $product_id );

	// endregion

	// region HOOKS

	/**
	 * Returns a field's value that was queried via the tab component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   null|mixed  $value          The value so far.
	 * @param   string      $field_id       The prefixed field ID.
	 * @param   int         $product_id     The product ID.
	 *
	 * @return  array|string|null
	 */
	public function maybe_get_field_value( $value, string $field_id, int $product_id ) {
		$return = $value;

		if ( \is_null( $return ) ) {
			$field_prefix = Strings::maybe_suffix( $this->get_group_name(), '/' );
			if ( Strings::starts_with( $field_id, $field_prefix ) ) {
				$return = $this->get_field_value( Strings::maybe_unprefix( $field_id, $field_prefix ), $product_id );
			}
		}

		return $return;
	}

	/**
	 * Retrieves a field's value that was queried via the tab component and runs it through a validation callback.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   null|mixed  $value          The value so far.
	 * @param   string      $field_id       The prefixed field ID.
	 * @param   int         $product_id     The product ID.
	 *
	 * @return  mixed
	 */
	public function maybe_get_validated_field_value( $value, string $field_id, int $product_id ) {
		$return = $value;

		if ( \is_null( $return ) ) {
			$field_prefix = Strings::maybe_suffix( $this->get_group_name(), '/' );
			if ( Strings::starts_with( $field_id, $field_prefix ) ) {
				$return = $this->get_validated_field_value( Strings::maybe_unprefix( $field_id, $field_prefix ), $product_id );
			}
		}

		return $return;
	}

	/**
	 * Updates a field's value that was updated via the tab component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $updated        Whether the value was updated already or not.
	 * @param   string  $field_id       The prefixed field ID.
	 * @param   int     $product_id     The ID of the product to update.
	 * @param   mixed   $value          Value to update to.
	 *
	 * @return bool
	 */
	public function maybe_update_field_value( bool $updated, string $field_id, int $product_id, $value ): bool {
		$return = $updated;

		if ( false === $updated ) {
			$field_prefix = Strings::maybe_suffix( $this->get_group_name(), '/' );
			if ( Strings::starts_with( $field_id, $field_prefix ) ) {
				$this->update_field_value( Strings::maybe_unprefix( $field_id, $field_prefix ), $product_id, $value );
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * Deletes a field's value that was deleted via the tab component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $deleted        Whether the value was deleted already or not.
	 * @param   string  $field_id       The prefixed field ID.
	 * @param   int     $product_id     The ID of the product to delete from.
	 *
	 * @return  bool
	 */
	public function maybe_delete_field_value( bool $deleted, string $field_id, int $product_id ): bool {
		$return = $deleted;

		if ( false === $deleted ) {
			$field_prefix = Strings::maybe_suffix( $this->get_group_name(), '/' );
			if ( Strings::starts_with( $field_id, $field_prefix ) ) {
				$this->delete_field_value( Strings::maybe_unprefix( $field_id, $field_prefix ), $product_id );
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * Validates a given value assuming it belongs to the given field it and that the validation was triggered via the
	 * tab component.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed       $value          The value to validate.
	 * @param   string      $field_id       The prefixed field ID.
	 * @param   int|null    $product_id     The ID of the product to validate for.
	 *
	 * @return  mixed
	 */
	public function maybe_validate_field_value( $value, string $field_id, ?int $product_id = null ) {
		$field_prefix = Strings::maybe_suffix( $this->get_group_name(), '/' );
		if ( Strings::starts_with( $field_id, $field_prefix ) ) {
			$value = $this->validate_field_value( $value, Strings::maybe_unprefix( $field_id, $field_prefix ), $product_id );
		}

		return $value;
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
			$field_id = $this->ungenerate_meta_key( $meta_key );
			$fields   = $this->get_group_fields();

			if ( isset( $fields[ $field_id ] ) ) {
				$value = $this->get_default_value( $field_id );
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

			foreach ( \array_keys( $this->get_group_fields() ) as $field_id ) {
				$meta_key = $this->generate_meta_key( $field_id );
				if ( ! isset( $existing_meta_keys[ $meta_key ] ) ) {
					// phpcs:disable WordPress.DB.SlowDBQuery
					$meta_data[] = (object) array(
						'meta_id'    => 0,
						'meta_key'   => $meta_key,
						'meta_value' => $this->get_default_value( $field_id ),
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
	 * Children classes should define the validation logic for their fields in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed       $value          Value to validate.
	 * @param   string      $field_id       The ID of the field that the value belongs to.
	 * @param   int|null    $product_id     The ID of the product to validate for. Optional.
	 *
	 * @return  mixed
	 */
	abstract protected function validate_field_value_helper( $value, string $field_id, ?int $product_id = null );

	/**
	 * Child classes should return their fields definitions here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array[]
	 */
	abstract protected function get_group_fields_helper(): array;

	// endregion
}
