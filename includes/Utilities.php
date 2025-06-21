<?php
namespace Sreshto\SwiftCoupons;

/**
 * Utilities class for the SwiftCoupons plugin.
 *
 * Provides helper methods for URL and path retrieval, error handling, 
 * and data sanitization within the plugin.
 *
 * @since 1.0.0
 * @package Sreshto\SwiftCoupons
 * @author Rehan Adil
 */
class Utilities
{
	// Static instance of the class
	public static $instance;

	// Private constructor to prevent direct instantiation
	private function __construct() {}

	/**
	 * Retrieves the singleton instance of the Utilities class.
	 *
	 * Ensures that only one instance of the class exists throughout the application.
	 *
	 * @since 1.0.0
	 * @author Rehan Adil
	 * @return Utilities The singleton instance of the Utilities class.
	 */
	public static function instance()
	{
		// Check if the instance is null
		if ( is_null( self::$instance ) )
		{
			// Create a new instance if it doesn't exist
			self::$instance = new self();
		}

		// Return the singleton instance
		return self::$instance;
	}

	/**
	 * Retrieves a URL within the SwiftCoupons plugin directory.
	 *
	 * Defaults to the SwiftCoupons plugins directory URL if no arguments are supplied.
	 *
	 * @since 1.0.0
	 * @author Rehan Adil
	 * @param string $path Optional. Extra path appended to the end of the URL. Default empty.
	 * @return string SwiftCoupons plugins URL link with optional paths appended.
	 */
	public static function url( $path = '' )
	{
		// Generate and return the plugin URL with the optional path appended
		return plugins_url( $path, SWIFT_COUPON_BASE_FILE );
	}

	/**
	 * Retrieves a file path within the SwiftCoupons plugin directory.
	 *
	 * Defaults to the SwiftCoupons plugins directory path if no arguments are supplied.
	 *
	 * @since 1.0.0
	 * @author Rehan Adil
	 * @param string $path Optional. Extra path appended to the end of the file path. Default empty.
	 * @return string SwiftCoupons plugins file path with optional paths appended.
	 */
	public static function path( $path = '' )
	{
		// Return the base path with the optional path appended
		return SWIFT_COUPON_BASE_PATH . $path;
	}

	/**
	 * Throws an exception and adds an error notice in WooCommerce.
	 *
	 * Useful for handling errors and notifying users in the WooCommerce environment.
	 *
	 * @since 1.0.0
	 * @author Rehan Adil
	 * @param string $message The error message to display and throw.
	 * @throws \Exception The exception with the provided message.
	 */
	public static function throw_exception_and_notice( $message )
	{
		// Add an error notice in WooCommerce
		wc_add_notice( $message, 'error' );
		// Throw an exception with the provided message
		throw new \Exception( $message );
	}

	/**
	 * Recursively sanitizes an array.
	 *
	 * Sanitizes keys and values in the array based on their data types.
	 *
	 * @since 1.0.0
	 * @author Rehan Adil
	 * @param array $array The array to sanitize.
	 * @return array The sanitized array.
	 */
	public static function sanitize_array( $array )
	{
		// Iterate through each key-value pair in the array
		foreach ( $array as $key => $value )
		{
			// Sanitize the key (always a string)
			$sanitized_key = sanitize_key( $key );

			// If the sanitized key is different, update the array
			if ( $sanitized_key !== $key )
			{
				// Remove the old key-value pair
				unset( $array[ $key ] );
				// Use the sanitized key
				$key = $sanitized_key;
			}

			// Sanitize the value based on its type
			if ( is_array( $value ) )
			{
				// Recursively sanitize arrays
				$array[ $key ] = self::sanitize_array( $value );
			}
			elseif ( is_int( $value ) )
			{
				// Sanitize integers
				$array[ $key ] = filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
			}
			elseif ( is_float( $value ) )
			{
				// Sanitize floats
				$array[ $key ] = filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			}
			elseif ( is_bool( $value ) )
			{
				// No need to sanitize booleans, just cast to boolean
				$array[ $key ] = (bool) $value;
			}
			elseif ( is_string( $value ) )
			{
				// Sanitize strings
				$array[ $key ] = sanitize_text_field( $value );
			}
			else
			{
				// For unknown types, set the value to null
				$array[ $key ] = null;
			}
		}

		// Return the sanitized array
		return $array;
	}

	/**
	 * Recursively escapes an array for safe output in WordPress, retaining data types.
	 *
	 * @since 1.0.0
	 * @author Rehan Adil
	 * @param array $array The array to escape.
	 * @return array The escaped array.
	 */
	public static function esc_array( $array )
	{
		$escaped = array();

		foreach ( $array as $key => $value )
		{
			// Escape the key for output
			$escaped_key = is_int( $key ) ? absint( $key ) : esc_html( $key );

			if ( is_array( $value ) )
			{
				$escaped[ $escaped_key ] = self::esc_array( $value );
			}
			elseif ( is_int( $value ) )
			{
				// Ensure integer type
				$escaped[ $escaped_key ] = intval( $value );
			}
			elseif ( is_float( $value ) )
			{
				// Ensure float type
				$escaped[ $escaped_key ] = floatval( $value );
			}
			elseif ( is_bool( $value ) )
			{
				// Ensure boolean type
				$escaped[ $escaped_key ] = boolval( $value );
			}
			elseif ( is_string( $value ) )
			{
				$escaped[ $escaped_key ] = esc_html( $value );
			}
			else
			{
				$escaped[ $escaped_key ] = null;
			}
		}

		return $escaped;
	}
}
