<?php
namespace Sreshto\SwiftCoupon\Qualifier\Rule;

/**
 * Class Product_Meta
 *
 * Handles the logic for matching product meta data against rules.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupon\Qualifier\Rule
 */
class Product_Meta extends Rule_Base
{
	/**
	 * Checks if the product meta matches the rule.
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
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 * @param array $rules The array of existing rules.
	 *
	 * @return array The modified array with the new rule registered.
	 */
	public function register_rule( $rules )
	{
		// Add the Product_Meta rule to the rules array.
		$rules[ 'Product_Meta' ] = [ 
			'id'          => 'Product_Meta',
			'category_id' => 'Product',
			'title'       => __( 'Product Meta', 'swift-coupons' ),
			'description' => __( 'Checks if the product meta matches the rule.', 'swift-coupons' ),
			'locked'      => true,
		];

		// Return the modified rules array.
		return $rules;
	}
}
