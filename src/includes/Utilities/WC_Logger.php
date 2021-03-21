<?php

namespace DeepWebSolutions\Framework\WooCommerce\Utilities;

use DeepWebSolutions\Framework\Foundations\Logging\LoggingHandlerInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\StoreableTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Wrapper around the WC_Logger class in order to use the WC Logger as a PSR-3 logger.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Utilities
 */
class WC_Logger extends \WC_Logger implements LoggingHandlerInterface, PluginAwareInterface {
	// region TRAITS

	use PluginAwareTrait;
	use StoreableTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * Logger constructor.
	 *
	 * @param   string          $handler_id     The ID of the logger.
	 * @param   array|null      $handlers       Array of log handlers.
	 * @param   string|null     $threshold      Define an explicit threshold.
	 *
	 * @see     WC_Logger::__construct()
	 */
	public function __construct( string $handler_id, $handlers = null, $threshold = null ) {
		$this->storeable_id = $handler_id;
		parent::__construct( $handlers, $threshold );
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the type of the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'wc-logging';
	}

	/**
	 * Sets the context source to the plugin's slug automatically.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpDocSignatureInspection
	 * @noinspection PhpMissingParamTypeInspection
	 *
	 * @param   string  $level      A PSR-3 compliant log level.
	 * @param   string  $message    Log message.
	 * @param   array   $context    Additional information for log handlers.
	 *
	 * @see     WC_Logger::log()
	 */
	public function log( $level, $message, $context = array() ) {
		$context['source'] = $this->get_plugin()->get_plugin_slug() . '.' . $this->get_id();
		parent::log( $level, $message, $context );
	}

	// endregion
}
