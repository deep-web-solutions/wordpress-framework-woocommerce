<?php

namespace DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities;

use DeepWebSolutions\Framework\Core\AbstractPluginFunctionality;
use DeepWebSolutions\Framework\Core\Actions\Installable\UninstallFailureException;
use DeepWebSolutions\Framework\Core\Actions\UninstallableInterface;
use DeepWebSolutions\Framework\Helpers\DataTypes\Strings;
use DeepWebSolutions\Framework\Utilities\Hooks\Actions\SetupHooksTrait;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;

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
abstract class WC_AbstractValidatedProductSettingsTabFunctionality extends AbstractPluginFunctionality implements UninstallableInterface {
	// region TRAITS

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
		$hooks_service->add_filter( 'woocommerce_product_data_tabs', $this, 'register_tab' );
		$hooks_service->add_action( 'woocommerce_product_data_panels', $this, 'output_tab_panel' );
		$hooks_service->add_action( 'woocommerce_process_product_meta', $this, 'save_tab_fields' );
	}

	// endregion

	// region CRUD

	/**
	 * Attempts to retrieve the raw value of a field for a given product.
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

		return \apply_filters( $this->get_hook_tag( 'get_field_value' ), null, $field_id, $product_id );
	}

	/**
	 * Attempts to retrieve the value of a field from a given product and validates it.
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
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return null;
		}

		return \apply_filters( $this->get_hook_tag( 'get_validated_field_value' ), null, $field_id, $product_id );
	}

	/**
	 * Attempts to update the raw value of a given field for a given product.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id       The ID of the field to update.
	 * @param   int     $product_id     The ID of the product to update it for.
	 * @param   mixed   $value          The new field value.
	 *
	 * @return bool
	 */
	public function update_field_value( string $field_id, int $product_id, $value ): bool {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return false;
		}

		return \apply_filters( $this->get_hook_tag( 'update_field_value' ), false, $field_id, $product_id, $value );
	}

	/**
	 * Attempts to delete  the raw value of a given field for a given product.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $field_id       The ID of the field to delete.
	 * @param   int     $product_id     The ID of the product to delete it from.
	 *
	 * @return  bool
	 */
	public function delete_field_value( string $field_id, int $product_id ): bool {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return false;
		}

		return \apply_filters( $this->get_hook_tag( 'delete_option_value' ), false, $field_id, $product_id );
	}

	/**
	 * Attempts to validate a given value assuming it belongs to a given field.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed       $value          The value to validate.
	 * @param   string      $field_id       The ID of the field the value is supposed to belong to.
	 * @param   int|null    $product_id     The ID of the product to validate it for. Optional.
	 *
	 * @return  mixed|null
	 */
	public function validate_field_value( $value, string $field_id, ?int $product_id = null ) {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return null;
		}

		return \apply_filters( $this->get_hook_tag( 'validate_field_value' ), $value, $field_id, $product_id, $value );
	}

	// endregion

	// region METHODS

	/**
	 * Children classes can override this helper to restrict the tab only to selected products. By default, returns true for all existent products.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $product_id     The ID of the product to check support for.
	 *
	 * @return  bool|null
	 */
	public function is_supported_product( int $product_id ): ?bool {
		$is_supported_product = null;

		$product_type = \WC_Product_Factory::get_product_type( $product_id );
		if ( false !== $product_type ) {
			$is_supported_product = true;
		}

		return \apply_filters( $this->get_hook_tag( 'is_supported_product' ), $is_supported_product, $product_id );
	}

	/**
	 * Returns the prefix of all the meta fields registered by this functionality.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	abstract public function get_meta_key_prefix(): string;

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

	// region INSTALLATION

	/**
	 * Removes all the metadata registered by this functionality from the database.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  UninstallFailureException|null
	 */
	public function uninstall(): ?UninstallFailureException {
		global $wpdb;

		/* @noinspection SqlNoDataSourceInspection */
		$result = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"DELETE FROM $wpdb->postmeta WHERE meta_key LIKE %s",
				$this->get_meta_key_prefix() . '%'
			)
		);
		if ( false === $result ) {
			return new UninstallFailureException( \__( 'Failed to delete product meta data from the database', 'dws-wp-framework-woocommerce' ) );
		}

		return null;
	}

	// endregion

	// region HOOKS

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
	public function register_tab( array $tabs ): array {
		$tabs[ $this->get_tab_slug() ] = array(
			'label'    => $this->get_tab_title(),
			'target'   => "{$this->get_tab_slug()}_product_data",
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
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @retrun  void
	 */
	public function output_tab_panel(): void {
		global $product_object, $thepostid;

		if ( $product_object instanceof \WC_Product && true === $this->is_supported_product( $product_object->get_id() ) ) : ?>

			<div id="<?php echo \esc_attr( "{$this->get_tab_slug()}_product_data" ); ?>" class="panel woocommerce_options_panel">
				<?php \do_action( $this->get_hook_tag( 'panel', 'before_options_groups' ) ); ?>

				<?php foreach ( $this->get_children() as $child ) : ?>
					<?php if ( $child instanceof WC_AbstractValidatedProductSettingsGroupFunctionality && true === $child->is_supported_product( $product_object->get_id() ) ) : ?>
						<?php \do_action( $this->get_hook_tag( 'panel', array( 'before_options_group', $child->get_group_name() ) ) ); ?>

						<div class="options_group <?php echo \esc_attr( \join( ' ', $child->get_group_classes() ) ); ?>">
							<?php
							foreach ( $child->get_group_fields() as $field_id => $field ) {
								$meta_key    = $child->generate_meta_key( $field_id );
								$field_extra = array(
									'id'    => Strings::maybe_unprefix( \str_replace( '-', '_', $meta_key ) ),
									'name'  => $meta_key,
									'value' => \get_post_meta( $thepostid, $meta_key, true ),
								);

								switch ( $field['type'] ?? 'text' ) {
									case 'text':
										\woocommerce_wp_text_input( $field + $field_extra );
										break;
									case 'textarea':
										\woocommerce_wp_textarea_input( $field + $field_extra );
										break;
									/* @noinspection PhpMissingBreakStatementInspection */
									case 'multiselect':
										$field_extra['name']             .= '[]';
										$field_extra['style']             = 'width: 50%;';
										$field_extra['custom_attributes'] = array( 'multiple' => 'multiple' );
										// A multi-select is basically a select with some extra attributes.
									case 'select':
										\woocommerce_wp_select( $field + $field_extra );
										break;
									case 'radio':
										\woocommerce_wp_radio( $field + $field_extra );
										break;
									case 'checkbox':
										\woocommerce_wp_checkbox( $field + $field_extra );
										break;
									case 'hidden':
										\woocommerce_wp_hidden_input( $field + $field_extra );
										break;
									default:
										\do_action( $this->get_hook_tag( 'panel', array( 'output_field', $field['type'] ) ), $field_id, $field + $field_extra );
								}
							}
							?>
						</div>

						<?php \do_action( $this->get_hook_tag( 'panel', array( 'after_options_group', $child->get_group_name() ) ) ); ?>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php \do_action( $this->get_hook_tag( 'panel', 'after_options_groups' ) ); ?>
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
	public function save_tab_fields( int $product_id ): void {
		if ( true !== $this->is_supported_product( $product_id ) ) {
			return;
		}

		$product = \wc_get_product( $product_id );

		foreach ( $this->get_children() as $child ) {
			if ( $child instanceof WC_AbstractValidatedProductSettingsGroupFunctionality && true === $child->is_supported_product( $product_id ) ) {
				$child->save_group_fields( $product );
			}
		}

		$product->save_meta_data();
	}

	// endregion
}
