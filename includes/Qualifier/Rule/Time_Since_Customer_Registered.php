<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use Sreshto\SwiftCoupons\Qualifier\Rule\Rule_Base;

/**
 * Class Time_Since_Customer_Registered
 *
 * Checks if the current user registered within a specified number of time.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Time_Since_Customer_Registered extends Rule_Base
{
	/**
	 * Checks if the current user registered within the specified time.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 *
	 * @return bool The result of the match.
	 */
	public function match()
	{
		return false;
	}

	/**
	 * Registers the rule in the system.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param array $rules The array of existing rules.
	 * @return array The modified array with the new rule registered.
	 */
	public function register_rule( $rules )
	{
		$rules[ 'Time_Since_Customer_Registered' ] = [ 
			'id'          => 'Time_Since_Customer_Registered',
			'category_id' => 'Customer',
			'title'       => __( 'Time Since Customer Registered', 'swift-coupons-for-woocommerce' ),
			'description' => __( 'Checks if the customer registered within a specified time.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false,
			'lock_type'   => parent::LOCKED_PREMIUM,
		];
		return $rules;
	}
}
