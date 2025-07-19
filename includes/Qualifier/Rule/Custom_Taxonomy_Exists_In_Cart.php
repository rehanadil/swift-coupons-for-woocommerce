<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use Sreshto\SwiftCoupons\Qualifier\Rule\Rule_Base;

/**
 * Class Custom_Taxonomy_Exists_In_Cart
 *
 * Checks if a custom taxonomy term exists in any cart item in the WooCommerce cart.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Custom_Taxonomy_Exists_In_Cart extends Rule_Base
{
	/**
	 * Checks if the custom taxonomy term exists in the cart items.
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
		$rules[ 'Custom_Taxonomy_Exists_In_Cart' ] = [ 
			'id'          => 'Custom_Taxonomy_Exists_In_Cart',
			'category_id' => 'Cart',
			'title'       => __( 'Custom Taxonomy Exists In Cart', 'swift-coupons-for-woocommerce' ),
			'description' => __( 'Checks if a custom taxonomy term exists in any cart item.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false,
			'lock_type'   => parent::LOCKED_PREMIUM,
		];
		return $rules;
	}
}
