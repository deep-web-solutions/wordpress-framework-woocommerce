<?php

namespace DeepWebSolutions\Framework\WooCommerce;

\defined( 'ABSPATH' ) || exit;

/**
 * Collection of useful static helpers to be used throughout the projects.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\WooCommerce
 */
final class WC_Helpers {
	/**
	 * Returns the code version of WC.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public static function get_version(): string {
		return \defined( 'WC_VERSION' ) ? WC_VERSION : '0.0.0';
	}

	/**
	 * Checks if the running version of WC is exactly the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_version( string $version ): bool {
		return 0 === \version_compare( self::get_version(), $version, null );
	}

	/**
	 * Checks if the running version of WC is older than the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_version_lt( string $version ): bool {
		return \version_compare( $version, self::get_version(), '>' );
	}

	/**
	 * Checks if the running version of WC is older or the same as the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_version_lte( string $version ): bool {
		return \version_compare( $version, self::get_version(), '>=' );
	}

	/**
	 * Checks if the running version of WC is newer the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_version_gt( string $version ): bool {
		return \version_compare( self::get_version(), $version, '>' );
	}

	/**
	 * Checks if the running version of WC is newer or the same as the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_version_gte( string $version ): bool {
		return \version_compare( self::get_version(), $version, '>=' );
	}

	/**
	 * Returns the database version of WC.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public static function get_db_version(): string {
		return \get_option( 'woocommerce_db_version', '0.0.0' );
	}

	/**
	 * Checks if the version of the WC database is exactly the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_db_version( string $version ): bool {
		return 0 === \version_compare( self::get_db_version(), $version, null );
	}

	/**
	 * Checks if the version of the WC database is older than the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_db_version_lt( string $version ): bool {
		return \version_compare( $version, self::get_db_version(), '>' );
	}

	/**
	 * Checks if the version of the WC database is older or the same as the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_db_version_lte( string $version ): bool {
		return \version_compare( $version, self::get_db_version(), '>=' );
	}

	/**
	 * Checks if the version of the WC database is newer the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_db_version_gt( string $version ): bool {
		return \version_compare( self::get_db_version(), $version, '>' );
	}

	/**
	 * Checks if the version of the WC database is newer or the same as the given one.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $version    Version to check for.
	 *
	 * @return  bool
	 */
	public static function is_db_version_gte( string $version ): bool {
		return \version_compare( self::get_db_version(), $version, '>=' );
	}
}
