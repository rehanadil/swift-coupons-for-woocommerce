<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Cart_Subtotal
 *
 * Handles the rule for matching the cart subtotal with specified conditions.
 *
 * @since 1.0
 * @version 1.0
 * @author Rehan Adil
 */
class Cart_Subtotal extends Rule_Base
{
	/**
	 * Checks if the cart subtotal matches the rule.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @return bool The result of the match.
	 */
	public function match()
	{
		// Get the logic from the rule.
		$logic = $this->get_data( 'logic' );

		// Get the amount from the rule and convert it to a float.
		$amount = floatval( $this->get_data( 'amount' ) );

		// Get the cart subtotal from WooCommerce.
		$cart_subtotal = WC()->cart->subtotal;

		// Set the difference variable for display purposes.
		$this->set_variable( 'diff', get_woocommerce_currency_symbol() . abs( $cart_subtotal - $amount ) );

		// Set the amount variable for display purposes.
		$this->set_variable( 'amount', get_woocommerce_currency_symbol() . $amount );

		// Return the comparison result based on the logic and values.
		return $this->compare( $logic, $cart_subtotal, $amount );
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
		// Add the Cart_Subtotal rule to the rules array.
		$rules[ 'Cart_Subtotal' ] = [ 
			'id'                    => 'Cart_Subtotal',
			'category_id'           => 'Cart',
			'title'                 => __( 'Cart Subtotal', 'swift-coupons-for-woocommerce' ),
			'description'           => __( 'The subtotal of the cart must match the rules you set below to use this coupon.', 'swift-coupons-for-woocommerce' ),
			'default_error_message' => __( 'The cart subtotal must be {logic} {amount}.', 'swift-coupons-for-woocommerce' ),
			'inputs'                => [ 
				[ 
					'size'    => 1 / 2,
					'type'    => 'select',
					'name'    => 'logic',
					'options' => $this->get_logic_options( 'compare' ),
				],
				[ 
					'size'        => 1 / 2,
					'type'        => 'number',
					'name'        => 'amount',
					'value'       => '',
					'label'       => __( 'Amount', 'swift-coupons-for-woocommerce' ),
					'placeholder' => __( 'Total amount in the cart', 'swift-coupons-for-woocommerce' ),
				],
			],
			'unlocked'              => true, // Indicates if the rule is locked.
			'lock_type'             => null, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
