<?php
namespace Sreshto\SwiftCoupon\Qualifier\Rule;

/**
 * Class Customer_Order_Count
 *
 * This class defines a rule to check the customer's order count against a specified condition.
 *
 * @since 1.0
 * @version 1.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupon\Qualifier\Rule
 */
class Customer_Order_Count extends Rule_Base
{
	/**
	 * Checks if the customer's order count matches the rule.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @return bool True if the rule matches, false otherwise.
	 */
	public function match()
	{
		return false;
	}

	/**
	 * Registers the rule in the system.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 * @param array $rules The array of existing rules.
	 *
	 * @return array The modified array with the new rule added.
	 */
	public function register_rule( $rules )
	{
		// Add the Customer_Order_Count rule to the rules array.
		$rules[ 'Customer_Order_Count' ] = [ 
			'id'          => 'Customer_Order_Count',
			'category_id' => 'Customer',
			'title'       => __( 'Customer Order Count', 'swift-coupons' ),
			'description' => __( 'The customer must have this number of existing orders, for example, if you set to more than 3, the coupon will be valid from the 4th order and so on. If you set it to exactly 5, the coupon will be valid only for the 6th order.', 'swift-coupons' ),
			'locked'      => true,
		];

		// Return the modified rules array.
		return $rules;
	}
}
