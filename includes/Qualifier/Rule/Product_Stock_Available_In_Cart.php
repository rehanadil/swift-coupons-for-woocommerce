<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use Sreshto\SwiftCoupons\Qualifier\Rule\Rule_Base;

/**
 * Class Product_Stock_Available_In_Cart
 *
 * This class defines a rule for checking if the product stock is available in the cart.
 *
 * @since 1.0
 * @version 1.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Product_Stock_Available_In_Cart extends Rule_Base
{
	/**
	 * Checks if the product stock is available in the cart based on rule settings.
	 *
	 * Retrieves the products and stock check logic from the rule data, 
	 * and verifies if the specified products have sufficient stock available.
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
	 * Adds the Product_Stock_Available_In_Cart rule to the list of available rules with its metadata,
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
		// Add the Product_Stock_Available_In_Cart rule to the rules array.
		$rules[ 'Product_Stock_Available_In_Cart' ] = [ 
			// The ID of the rule.
			'id'          => 'Product_Stock_Available_In_Cart',
			// The category ID of the rule.
			'category_id' => 'Product',
			// The title of the rule.
			'title'       => __( 'Product Stock Available In Cart', 'swift-coupons-for-woocommerce' ),
			// The description of the rule.
			'description' => __( 'This rule checks if specified products have sufficient stock available in the cart.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
