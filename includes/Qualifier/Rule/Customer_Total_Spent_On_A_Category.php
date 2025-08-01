<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use WC_Customer, Sreshto\SwiftCoupons\Qualifier\Rule\Rule_Base;

/**
 * Class Customer_Total_Spent_On_A_Category
 *
 * This class defines a rule to check if a customer has spent a specific total amount in all previous orders.
 *
 * @since 1.0
 * @version 1.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Customer_Total_Spent_On_A_Category extends Rule_Base
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
		// Add the Customer_Total_Spent_On_A_Category rule to the rules array.
		$rules[ 'Customer_Total_Spent_On_A_Category' ] = [ 
			// The ID of the rule.
			'id'          => 'Customer_Total_Spent_On_A_Category',
			// The category of the rule.
			'category_id' => 'Customer',
			// The title of the rule.
			'title'       => __( 'Customer Total Spent On A Category', 'swift-coupons-for-woocommerce' ),
			// The description of the rule.
			'description' => __( 'The customer must have spent the specified amount on products from a particular category previously.' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
