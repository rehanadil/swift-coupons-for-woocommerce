<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

use Sreshto\SwiftCoupons\Qualifier\Rule\Rule_Base;

/**
 * Class Shipping_Zone_And_Region
 *
 * This class defines a rule to check if a customer's shipping zone/region matches the specified criteria.
 *
 * @since 1.0
 * @version 1.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Shipping_Zone_And_Region extends Rule_Base
{
	/**
	 * Checks if the customer's shipping zone/region matches the rule
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @return bool The result of the match
	 */
	public function match()
	{
		return false;
	}

	/**
	 * Registers the rule
	 *
	 * Adds the Shipping_Zone_And_Region rule to the list of available rules.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 * @param array $rules The array of rules
	 * @return array The modified array with the new rule to be registered
	 */
	public function register_rule( $rules )
	{
		$rules[ 'Shipping_Zone_And_Region' ] = [ 
			'id'          => 'Shipping_Zone_And_Region',
			'category_id' => 'Shipping',
			'title'       => __( 'Shipping Zone & Region', 'swift-coupons-for-woocommerce' ),
			'description' => __( 'Filter coupon validity by shipping zones or regions. Can be used to allow or restrict certain zones/regions from using this coupon.', 'swift-coupons-for-woocommerce' ),
			'unlocked'    => false,
			'lock_type'   => parent::LOCKED_PREMIUM, // Type of lock for the rule.
		];

		return $rules;
	}
}
