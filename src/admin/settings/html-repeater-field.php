<?php
/**
 * Outputs the HTML for a repeater settings field.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Settings
 *
 * @var     array   $field              The field definition.
 * @var     array   $subfields          The subfields definition.
 * @var     array   $value              The field's current value.
 * @var     array   $default_row_value  A new row's default value.
 *
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

defined( 'ABSPATH' ) || exit;

$tooltip_html = ! empty( $field['desc_tip'] ?? '' ) ? wc_help_tip( $field['desc_tip'] ) : '';

?>

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $field['id'] ); ?>">
			<?php echo esc_html( $field['title'] ); ?>
			<?php echo wp_kses_post( $tooltip_html ); ?>
		</label>
	</th>
	<td id="<?php echo esc_attr( $field['id'] ); ?>" class="forminp forminp-dws-repeater forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
		<div class="wc_input_table_wrapper">
			<table class="widefat wc_input_table sortable" cellspacing="0">
				<thead>
					<tr>
						<th class="sort">&nbsp;</th>
						<?php foreach ( $subfields as $subfield ) : ?>
						<th>
							<?php echo esc_html( $subfield['title'] ); ?>
						</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody data-rows="<?php echo wc_esc_json( wp_json_encode( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				</tbody>
				<tfoot>
					<tr>
						<th colspan="7">
							<a href="#" class="add button">
								<?php echo esc_html( $field['add_button'] ); ?>
							</a>
							<a href="#" class="remove_rows button">
								<?php echo esc_html( $field['remove_rows_button'] ); ?>
							</a>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<script type="text/html" id="tmpl-<?php echo \esc_attr( $field['id'] ); ?>-row">
			<tr class="<?php echo sanitize_html_class( $field['row_class'] ); ?>">
				<td class="sort"></td>
				<?php foreach ( $subfields as $subfield ) : ?>
				<td><?php echo $subfield['template']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				<?php endforeach; ?>
			</tr>
		</script>
		<script type="text/javascript">
			jQuery( function() {
				const $table = jQuery('#<?php echo \esc_attr( $field['id'] ); ?>'),
					$table_body = $table.find('tbody'),
					rows_data = $table_body.data('rows'),
					rows_template = wp.template('<?php echo \esc_attr( $field['id'] ); ?>-row');

				if ( Array.isArray( rows_data ) ) {
					rows_data.forEach( function( value, index ) {
						$table_body.append( rows_template({
							index: index,
							value: value
						}) );
					} );
				}

				$table.on( 'click', 'a.add', function() {
					const size = $table_body.find( 'tr' ).length;

					$table_body.append(
						rows_template( {
							index : size,
							value : <?php echo wp_json_encode( $default_row_value ); ?>
						} )
					);

					return false;
				} );
			} );
		</script>
	</td>
</tr>
