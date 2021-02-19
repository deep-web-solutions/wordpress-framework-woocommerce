<?php

namespace DeepWebSolutions\Framework\WooCommerce\Utilities;

use Psr\Log\LoggerInterface;
use WC_Logger;

defined( 'ABSPATH' ) || exit;

/**
 * Wrapper around the WC_Logger class in order to use the WC Logger as a PSR-3 logger.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce\Utilities
 */
class Logger extends WC_Logger implements LoggerInterface {
	/* empty on purpose */
}
