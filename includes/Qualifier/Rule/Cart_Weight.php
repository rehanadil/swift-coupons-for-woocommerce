<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Cart_Weight
 *
 * This class defines a rule for checking the total weight of items in the cart.
 *
 * @since 1.0
 * @version 1.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Cart_Weight extends Rule_Base
{
	/**
	 * Checks if the cart weight matches the rule.
	 *
	 * Retrieves the logic and weight from the rule data, calculates the cart weight,
	 * and compares it using the specified logic.
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
	 * Adds the Cart_Weight rule to the list of available rules with its metadata,
	 * inputs, and default error message.
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
		// Add the Cart_Weight rule to the rules array.
		$rules[ 'Cart_Weight' ] = [ 
			// The ID of the rule.
			'id'          => 'Cart_Weight',
			// The category ID of the rule.
			'category_id' => 'Cart',
			// The title of the rule.
			'title'       => __( 'Cart Weight', 'swift-coupons' ),
			// The description of the rule.
			'description' => __( 'The total weight of items in the cart must match the rules you set below to use this coupon.', 'swift-coupons' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
