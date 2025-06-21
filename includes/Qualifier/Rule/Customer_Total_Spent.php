<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use WC_Customer;

/**
 * Class Customer_Total_Spent
 *
 * This class defines a rule to check if a customer has spent a specific total amount in all previous orders.
 *
 * @since 1.0
 * @version 1.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Customer_Total_Spent extends Rule_Base
{
	/**
	 * Checks if the customer's total spent amount matches the rule.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @return bool The result of the match.
	 */
	public function match()
	{
		return false;
	}

	/**
	 * Registers the rule.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 * @param array $rules The array of rules.
	 *
	 * @return array The modified array with the new rule to be registered.
	 */
	public function register_rule( $rules )
	{
		// Add the Customer_Total_Spent rule to the rules array.
		$rules[ 'Customer_Total_Spent' ] = [ 
			'id'          => 'Customer_Total_Spent',
			'category_id' => 'Customer',
			'title'       => __( 'Customer Total Spent', 'swift-coupons-for-woocommerce' ),
			'description' => __( 'The customer must have spent this amount in all previous orders combined. For example, if you set to more than 100, the coupon will be valid after the customer has spent more than 100 in total.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
