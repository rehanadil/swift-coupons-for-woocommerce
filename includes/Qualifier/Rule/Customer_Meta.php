<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Customer_Meta
 *
 * Handles the logic for checking customer meta data against rules.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Customer_Meta extends Rule_Base
{
	/**
	 * Checks if the customer meta matches the rule.
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
		// Add the Customer_Meta rule to the rules array.
		$rules[ 'Customer_Meta' ] = [ 
			'id'          => 'Customer_Meta',
			'category_id' => 'Customer',
			'title'       => __( 'Customer Meta', 'swift-coupons-for-woocommerce' ),
			'description' => __( 'Checks if the customer user meta matches the rule.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false, // Indicates if the rule is locked.
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
