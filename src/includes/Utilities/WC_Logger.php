<?php

namespace DeepWebSolutions\Framework\WooCommerce\Utilities;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use Psr\Log\LoggerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Wrapper around the WC_Logger class in order to use the WC Logger as a PSR-3 logger.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Utilities
 */
class WC_Logger extends \WC_Logger implements LoggerInterface, PluginAwareInterface {
	// region TRAITS

	use PluginAwareTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * The name of the logger.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     string
	 */
	protected string $name;

	// endregion

	// region MAGIC METHODS

	/**
	 * Logger constructor.
	 *
	 * @param   string          $name       The name of the logger.
	 * @param   array|null      $handlers   Array of log handlers.
	 * @param   string|null     $threshold  Define an explicit threshold.
	 *
	 * @see     WC_Logger::__construct()
	 */
	public function __construct( string $name, $handlers = null, $threshold = null ) {
		$this->name = $name;
		parent::__construct( $handlers, $threshold );
	}

	// endregion

	// region INHERITED METHODS

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
		$context['source'] = $this->get_plugin()->get_plugin_slug() . '.' . $this->name;
		parent::log( $level, $message, $context );
	}

	// endregion
}
