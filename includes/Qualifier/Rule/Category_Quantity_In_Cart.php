<?php
namespace Sreshto\SwiftCoupon\Qualifier\Rule;

/**
 * Class Category_Quantity_In_Cart
 *
 * This class defines a rule to check if the quantity of products in a specific category
 * in the cart matches the specified logic and quantity.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Sreshto\SwiftCoupon\Qualifier\Rule
 */
class Category_Quantity_In_Cart extends Rule_Base
{
	/**
	 * Checks if the category quantity in cart matches the rule.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 *
	 * @return bool The result of the match.
	 */
	public function match()
	{
		// Get the rule data for category, logic, and quantity.
		$category = $this->get_data( 'category' );
		$logic    = $this->get_data( 'logic' );
		$quantity = $this->get_data( 'quantity' );

		// Calculate the total quantity of products in the specified category in the cart.
		$category_quantity_in_cart = array_reduce(
			WC()->cart->get_cart_contents(),
			function ($total, $cart_item) use ($category): int
			{
				// Get the product ID from the cart item.
				$product_id = absint( $cart_item[ 'product_id' ] );

				// Check if the product belongs to the specified category.
				if ( ! has_term( $category[ 'value' ], 'product_cat', $product_id ) )
				{
					// If not, return 0.
					return 0;
				}

				// Add the quantity of the product to the total.
				return $total + absint( $cart_item[ 'quantity' ] );
			},
			0, // Initial total is 0.
		);

		// Set the difference between the actual and required quantity as a variable for error messages.
		$this->set_variable( 'diff', absint( $category_quantity_in_cart - $quantity ) );

		// Compare the actual quantity with the required quantity using the specified logic.
		return $this->compare( $logic, $category_quantity_in_cart, $quantity );
	}

	/**
	 * Registers the rule in the system.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @param array $rules The array of rules to be registered.
	 *
	 * @return array The modified array with the new rule added.
	 */
	public function register_rule( $rules )
	{
		// Add the Category_Quantity_In_Cart rule to the rules array.
		$rules[ 'Category_Quantity_In_Cart' ] = [ 
			// Unique identifier for the rule.
			'id'                    => 'Category_Quantity_In_Cart',
			// Identifier for the category input field.
			'category_id'           => 'Category',
			// Title of the rule.
			'title'                 => __( 'Category Quantity In Cart', 'swift-coupons' ),
			// Description of the rule.
			'description'           => __( 'Checks if the category quantity in cart matches the rule.', 'swift-coupons' ),
			// Default error message for the rule.
			'default_error_message' => __( 'The cart must contain {logic} {quantity} products from {category.label} category.', 'swift-coupons' ),
			// Inputs required for the rule.
			'inputs'                => [ 
				[ 
					// Input for selecting a category via AJAX.
					'size' => 1 / 3,
					'type' => 'ajax-select',
					'name' => 'category',
					'url'  => '/wp-json/swift-coupons/v1/categories/search?query={query}',
				],
				[ 
					// Input for selecting the comparison logic.
					'size'    => 1 / 3,
					'type'    => 'select',
					'name'    => 'logic',
					'options' => $this->get_logic_options( 'compare' ),
				],
				[ 
					// Input for specifying the required quantity.
					'size'        => 1 / 3,
					'type'        => 'number',
					'name'        => 'quantity',
					'value'       => '',
					'label'       => __( 'Quantity', 'swift-coupons' ),
					'placeholder' => __( 'Total quantity of products in this category', 'swift-coupons' ),
				],
			],
		];

		// Return the modified rules array.
		return $rules;
	}
}
