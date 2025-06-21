<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Product_Quantity_In_Cart
 *
 * This class defines a rule to check if the quantity of a specific product in the cart matches the given condition.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Product_Quantity_In_Cart extends Rule_Base
{
	/**
	 * Checks if the cart item meta matches the rule.
	 *
	 * Retrieves the product, logic, and quantity from the rule data, calculates the product quantity in the cart,
	 * and compares it against the rule's logic and quantity.
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
	 * Adds the Product_Quantity_In_Cart rule to the list of available rules with its metadata and input fields.
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
		// Add the Product_Quantity_In_Cart rule to the rules array.
		$rules[ 'Product_Quantity_In_Cart' ] = [ 
			// Set the rule ID.
			'id'          => 'Product_Quantity_In_Cart',
			// Set the category ID for the rule.
			'category_id' => 'Product',
			// Set the title of the rule.
			'title'       => __( 'Product Quantity In Cart', 'swift-coupons-for-woocommerce' ),
			// Set the description of the rule.
			'description' => __( 'Checks if the product quantity in cart matches the rule.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
