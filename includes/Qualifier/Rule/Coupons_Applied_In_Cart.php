<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use Sreshto\SwiftCoupons\Qualifier\Rule\Rule_Base;

/**
 * Class Coupons_Applied_In_Cart
 *
 * This class defines a rule for checking if coupons are applied in the cart.
 *
 * @since 1.0
 * @version 1.0
 * @package Sreshto\SwiftCoupons\Premium\Qualifier\Rule
 */
class Coupons_Applied_In_Cart extends Rule_Base {
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
	public function match() {
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
	public function register_rule( $rules ) {
		// Add the Coupons_Applied_In_Cart rule to the rules array.
		$rules[ 'Coupons_Applied_In_Cart' ] = [ 
			// The ID of the rule.
			'id'          => 'Coupons_Applied_In_Cart',
			// The category ID of the rule.
			'category_id' => 'Cart',
			// The title of the rule.
			'title'       => __( 'Coupons Applied In Cart', 'swift-coupons-for-woocommerce' ),
			// The description of the rule.
			'description' => __( 'This rule checks if any coupons are applied in the cart.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
