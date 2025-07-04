<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Cart_Quantity
 *
 * This class defines a rule for checking the total quantity of items in the cart.
 *
 * @since 1.0
 * @version 1.0
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Cart_Quantity extends Rule_Base
{
	/**
	 * Checks if the cart quantity matches the rule.
	 *
	 * Retrieves the logic and amount from the rule, calculates the total cart quantity,
	 * and compares it against the rule's conditions.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @return bool The result of the match.
	 */
	public function match()
	{
		// Get the logic from the rule
		$logic = $this->get_data( 'logic' );

		// Get the amount from the rule
		$amount = $this->get_data( 'amount' );

		// Get the cart quantity by summing up the quantities of all cart items
		$cart_quantity = array_reduce( WC()->cart->get_cart_contents(), function ($total, $cart_item)
		{
			// Add the quantity of the current cart item to the total
			return $total + $cart_item[ 'quantity' ];
		}, 0 );

		// Set the difference between the cart quantity and the rule amount as a variable
		$this->set_variable( 'diff', $cart_quantity - $amount );

		// Return the comparison result based on the logic and values
		return $this->compare( $logic, $cart_quantity, $amount );
	}

	/**
	 * Registers the rule.
	 *
	 * Adds the Cart_Quantity rule to the list of available rules with its metadata,
	 * inputs, and default error message.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param array $rules The array of rules.
	 *
	 * @return array The modified array with the new rule to be registered.
	 */
	public function register_rule( $rules )
	{
		// Add the Cart_Quantity rule to the rules array
		$rules[ 'Cart_Quantity' ] = [ 
			'id'                    => 'Cart_Quantity',
			'category_id'           => 'Cart',
			'title'                 => __( 'Cart Quantity', 'swift-coupons-for-woocommerce' ),
			'description'           => __( 'The total quantity of items in the cart must match the rules you set below to use this coupon.', 'swift-coupons-for-woocommerce' ),
			'default_error_message' => __( 'The cart quantity must be {logic} {amount}.', 'swift-coupons-for-woocommerce' ),
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
					'placeholder' => __( 'Total number of items, if a product has 3 quantity then it will be counted as 3', 'swift-coupons-for-woocommerce' ),
				],
			],
			'unlocked'              => true, // Indicates if the rule is locked.
			'lock_type'             => null, // Type of lock for the rule.
		];

		// Return the modified rules array
		return $rules;
	}
}
