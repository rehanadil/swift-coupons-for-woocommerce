<?php
// Define the namespace for the class
namespace Sreshto\SwiftCoupons;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Class Scheduler
 *
 * Handles the scheduling logic for WooCommerce coupons.
 * Ensures that coupons are valid only within specified date ranges, days, and times.
 *
 * @package Sreshto\SwiftCoupons
 * @author Rehan Adil
 */
class Scheduler
{
	// Static property to hold the single instance of the class
	public static $instance;

	/**
	 * Scheduler constructor.
	 *
	 * @author Rehan Adil
	 */
	private function __construct()
	{
		// Hook the 'validate_coupon_schedule' method to the 'woocommerce_coupon_is_valid' action
		add_action( 'woocommerce_coupon_is_valid', [ $this, 'validate_coupon_schedule' ], 10, 2 );
	}

	/**
	 * Get the single instance of the Scheduler class.
	 *
	 * @return Scheduler The single instance of the Scheduler class.
	 * @author Rehan Adil
	 */
	public static function instance()
	{
		// Check if the instance is null, and create it if necessary
		if ( is_null( self::$instance ) )
		{
			self::$instance = new self();
		}

		// Return the single instance
		return self::$instance;
	}

	/**
	 * Check the schedule of a coupon to determine its validity.
	 *
	 * @param bool $valid The original validity of the coupon.
	 * @param \WC_Coupon $coupon The WooCommerce coupon object.
	 * @return bool True if the coupon is valid, false otherwise.
	 * @author Rehan Adil
	 */
	public function validate_coupon_schedule( $valid, $coupon )
	{
		// Get the schedule metadata for the coupon
		$schedule = get_post_meta( $coupon->get_id(), 'swiftcoupons_scheduler', true );

		// If no schedule or schedule is not an array, return the original validity
		if ( ! $schedule || ! is_array( $schedule ) )
			return $valid;

		// If scheduling is not enabled, return the original validity
		if ( ! $schedule[ 'enabled' ] )
			return $valid;

		// Get the current date
		$current_date = strtotime( current_time( 'Y-m-d' ) );

		// Check if the current date is within the scheduled start and end dates
		if ( $current_date < strtotime( $schedule[ 'start_date' ] ) || $current_date > strtotime( $schedule[ 'end_date' ] ) )
		{
			// Throw an exception and display a notice if the coupon is outside the date range
			Utilities::throw_exception_and_notice( __( 'This coupon is not valid because it is outside the scheduled date range.', 'swift-coupons-for-woocommerce' ) );
			return false;
		}

		// If all checks pass, return the original validity
		return apply_filters( 'swift-coupons/scheduler-valid', $valid, $schedule, $coupon );
	}
}
