<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use Sreshto\SwiftCoupons\Qualifier\Rule\Rule_Base;

/**
 * Class Time_Since_Customer_Last_Order
 *
 * Checks if the current user's last order was placed within a specified time.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Time_Since_Customer_Last_Order extends Rule_Base
{
	/**
	 * Checks if the current user's last order was placed within the specified time.
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
		$rules[ 'Time_Since_Customer_Last_Order' ] = [ 
			'id'          => 'Time_Since_Customer_Last_Order',
			'category_id' => 'Customer',
			'title'       => __( 'Time Since Customer Last Order', 'swift-coupons-for-woocommerce' ),
			'description' => __( 'Checks if the customer placed their last order within a specified time.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false,
			'lock_type'   => parent::LOCKED_PREMIUM,
		];
		return $rules;
	}
}
