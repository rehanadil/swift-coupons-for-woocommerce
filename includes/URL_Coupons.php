<?php
namespace Sreshto\SwiftCoupons; // Define the namespace for the class

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Class URL_Coupons
 *
 * Handles URL-based coupon application in WooCommerce.
 * - Adds custom rewrite rules for coupon URLs.
 * - Registers custom query variables.
 * - Applies coupons based on URL parameters.
 * - Supports redirecting users after coupon application.
 *
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupons
 */
class URL_Coupons
{
	// Static instance of the class
	public static $instance;

	// Private constructor to enforce singleton pattern
	/**
	 * URL_Coupons constructor.
	 * Registers WordPress hooks for rewrite rules, template redirection, and query variables.
	 *
	 * @author Rehan Adil
	 */
	private function __construct()
	{
		// Add rewrite rules on WordPress initialization
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		// Apply coupon from URL during template redirection
		add_action( 'template_redirect', array( $this, 'apply_coupon_from_url' ) );
		// Register custom query variables
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
	}

	/**
	 * Singleton instance method.
	 *
	 * @return URL_Coupons The single instance of the class.
	 * @author Rehan Adil
	 */
	public static function instance()
	{
		// Check if instance is null, create a new one if necessary
		if ( is_null( self::$instance ) )
		{
			self::$instance = new self();
		}

		// Return the instance
		return self::$instance;
	}

	/**
	 * Add custom rewrite rules for coupon URLs.
	 * Defines how URLs with the "coupon" prefix are handled.
	 *
	 * @author Rehan Adil
	 */
	public function add_rewrite_rules()
	{
		// Add a rewrite rule for coupon URLs
		add_rewrite_rule( '^coupon/([^/]*)/?', 'index.php?swiftcoupons_apply_coupon_code=$matches[1]', 'top' );
		// Add a rewrite tag for the coupon code
		add_rewrite_tag( '%swiftcoupons_apply_coupon_code%', '([^&]+)' );
	}

	/**
	 * Register custom query variables.
	 *
	 * @param array $vars The existing query variables.
	 * @return array The modified query variables including the custom one.
	 * @author Rehan Adil
	 */
	public function register_query_vars( $vars )
	{
		// Add the custom query variable to the list
		$vars[] = 'swiftcoupons_apply_coupon_code';
		return $vars;
	}

	/**
	 * Apply coupon based on the URL query variable.
	 * Checks for the custom query variable and applies the coupon if valid.
	 *
	 * @author Rehan Adil
	 */
	public function apply_coupon_from_url()
	{
		global $wp_query;

		// Check if the custom query variable is set
		if ( isset( $wp_query->query_vars[ 'swiftcoupons_apply_coupon_code' ] ) )
		{
			// Retrieve the coupon code from the query variable
			$coupon_code = $wp_query->query_vars[ 'swiftcoupons_apply_coupon_code' ];
			// Apply the coupon
			$this->apply_coupon( $coupon_code );
		}
	}

	/**
	 * Apply the coupon to the WooCommerce cart.
	 *
	 * @param string $coupon_code The coupon code to apply.
	 * @author Rehan Adil
	 */
	private function apply_coupon( $coupon_code )
	{
		// Check if WooCommerce cart is available
		if ( ! WC()->cart )
			return;

		// Create a WooCommerce coupon object
		$coupon = new \WC_Coupon( $coupon_code );

		// Check if the coupon exists
		if ( ! $coupon->get_id() )
		{
			// Query for a coupon with a custom meta key
			$query = new \WP_Query( array(
				'post_type'  => 'shop_coupon',
				'meta_key'   => 'swiftcoupons_url_apply_override_code',
				'meta_value' => $coupon_code,
				'fields'     => 'ids',
			) );

			// If no coupon is found, return
			if ( ! $query->have_posts() )
				return;

			// Get the coupon ID from the query results
			$coupon_id = $query->posts[ 0 ];

			// Create a WooCommerce coupon object with the coupon ID
			$coupon = new \WC_Coupon( $coupon_id );

			// If the coupon still doesn't exist, return
			if ( ! $coupon->get_id() )
				return;
		}

		// Check if the coupon is already applied
		if ( WC()->cart->has_discount( $coupon_code ) )
			return;

		// Retrieve metadata for the coupon
		$url_metadata = get_post_meta( $coupon->get_id(), 'swiftcoupons_url_apply', true );

		// Check if the coupon is enabled for URL application
		if ( ! $url_metadata[ 'enabled' ] )
			return;

		// Get the redirect URL from metadata or default to the cart URL
		$redirect_url = isset( $url_metadata[ 'redirect_to_url' ] ) ? $url_metadata[ 'redirect_to_url' ] : null;
		$redirect_url = empty( $redirect_url ) ? wc_get_cart_url() : $redirect_url;

		// Check if the user should be redirected back to the origin
		$redirect_back = isset( $url_metadata[ 'redirect_back_to_origin' ] ) ? $url_metadata[ 'redirect_back_to_origin' ] : false;

		// Add the coupon to the WooCommerce cart
		WC()->cart->add_discount( sanitize_text_field( $coupon->get_code() ) );

		// Redirect back to the referring URL if specified
		if ( $redirect_back )
		{
			$redirect_url = wp_get_referer();
			$redirect_url = empty( $redirect_url ) ? wc_get_cart_url() : $redirect_url;

			// Redirect to the referring URL
			if ( $redirect_url )
			{
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}

		// Redirect to the specified URL
		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Generate rewrite rules and flush them.
	 * Ensures that the rewrite rules are added and flushed.
	 *
	 * @author Rehan Adil
	 */
	public function generate_rewrite_rules()
	{
		// Add rewrite rules
		$this->add_rewrite_rules();
		// Flush rewrite rules
		\flush_rewrite_rules();
	}

	/**
	 * Flush rewrite rules.
	 * Forces WordPress to regenerate its rewrite rules.
	 *
	 * @author Rehan Adil
	 */
	public function flush_rewrite_rules()
	{
		\flush_rewrite_rules();
	}
}
