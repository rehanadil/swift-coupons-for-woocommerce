<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Customer_Has_Ordered_Products_Before
 *
 * This class defines a rule to check if a customer has ordered a specific product before.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Customer_Has_Ordered_Products_Before extends Rule_Base
{
	/**
	 * Checks if the cart item meta matches the rule.
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
	 * Registers the rule.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @param array $rules The array of rules.
	 *
	 * @return array The modified array with the new rule to be registered.
	 */
	public function register_rule( $rules )
	{
		// Add the 'Customer_Has_Ordered_Products_Before' rule to the rules array.
		$rules[ 'Customer_Has_Ordered_Products_Before' ] = [ 
			'id'          => 'Customer_Has_Ordered_Products_Before',
			'category_id' => 'Customer',
			'title'       => __( 'Has Ordered Products Before', 'swift-coupons-for-woocommerce' ),
			'description' => __( 'Checks if the customer has ordered specific products before.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		return $rules;
	}
}
