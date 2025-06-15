<?php
namespace Sreshto\SwiftCoupon\Qualifier\Rule;

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
			// The unique ID of the rule.
			'id'                    => 'Cart_Subtotal',

			// The category ID of the rule.
			'category_id'           => 'Cart',

			// The title of the rule displayed to the user.
			'title'                 => __( 'Cart Subtotal', 'swift-coupons' ),

			// The description of the rule explaining its purpose.
			'description'           => __( 'The subtotal of the cart must match the rules you set below to use this coupon.', 'swift-coupons' ),

			// The default error message shown when the rule fails.
			'default_error_message' => __( 'The cart subtotal must be {logic} {amount}.', 'swift-coupons' ),

			// The inputs required for the rule configuration.
			'inputs'                => [ 
				[ 
					// The size of the input field.
					'size'    => 1 / 2,

					// The type of input field (dropdown).
					'type'    => 'select',

					// The name of the input field.
					'name'    => 'logic',

					// The options for the dropdown field.
					'options' => $this->get_logic_options( 'compare' ),
				],
				[ 
					// The size of the input field.
					'size'        => 1 / 2,

					// The type of input field (number).
					'type'        => 'number',

					// The name of the input field.
					'name'        => 'amount',

					// The default value of the input field.
					'value'       => '',

					// The label for the input field.
					'label'       => __( 'Amount', 'swift-coupons' ),

					// The placeholder text for the input field.
					'placeholder' => __( 'Total amount in the cart', 'swift-coupons' ),
				],
			],
		];

		// Return the modified rules array.
		return $rules;
	}
}
