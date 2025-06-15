<?php
namespace Sreshto\SwiftCoupon\Qualifier\Rule;

/**
 * Class Cart_Item_Meta
 *
 * Handles the logic for checking cart item meta against specific rules.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Sreshto\SwiftCoupon\Qualifier\Rule
 */
class Cart_Item_Meta extends Rule_Base
{
	/**
	 * Checks if the cart item meta matches the rule.
	 *
	 * Retrieves the rule parameters and evaluates the cart items in the WooCommerce cart
	 * to determine if they match the specified conditions.
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
	 * Adds the Cart_Item_Meta rule to the list of available rules with its
	 * configuration and metadata.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 * @param array $rules The array of existing rules.
	 * @return array The modified array with the new rule registered.
	 */
	public function register_rule( $rules )
	{
		// Add the Cart_Item_Meta rule to the rules array.
		$rules[ 'Cart_Item_Meta' ] = [ 
			'id'          => 'Cart_Item_Meta',
			'category_id' => 'Cart',
			'title'       => __( 'Cart Item Meta', 'swift-coupons' ),
			'description' => __( 'Checks if the cart item meta matches the rule.', 'swift-coupons' ),
			'locked'      => true,
		];

		// Return the modified rules array.
		return $rules;
	}
}
