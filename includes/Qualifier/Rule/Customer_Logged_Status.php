<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Customer_Logged_Status
 *
 * This class defines a rule to check the logged-in status of a customer.
 *
 * @since 1.0
 * @version 1.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Customer_Logged_Status extends Rule_Base
{
	/**
	 * Checks if the customer's logged status matches the rule.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @return bool True if the rule matches, false otherwise.
	 */
	public function match()
	{
		// Get the status from the rule data.
		$status = $this->get_data( 'status' );

		// Determine the match based on the status.
		switch ( $status )
		{
			// If the status is 'logged_in', check if the user is logged in.
			case 'logged_in':
				return is_user_logged_in();

			// If the status is 'guest', check if the user is not logged in.
			case 'guest':
				return ! is_user_logged_in();

			// Default case returns false if the status is invalid.
			default:
				return false;
		}
	}

	/**
	 * Registers the rule in the system.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @param array $rules The array of existing rules.
	 *
	 * @return array The modified array with the new rule registered.
	 */
	public function register_rule( $rules )
	{
		// Add the 'Customer_Logged_Status' rule to the rules array.
		$rules[ 'Customer_Logged_Status' ] = [ 
			// The unique ID of the rule.
			'id'                    => 'Customer_Logged_Status',
			// The category under which the rule falls.
			'category_id'           => 'Customer',
			// The title of the rule.
			'title'                 => __( 'Customer Logged Status', 'swift-coupons-for-woocommerce' ),
			// A brief description of the rule.
			'description'           => __( 'Whether this coupon is applicable only for logged-in users or guests.', 'swift-coupons-for-woocommerce' ),
			// The default error message if the rule does not match.
			'default_error_message' => __( 'You are not allowed to use this coupon.', 'swift-coupons-for-woocommerce' ),
			// The inputs required for this rule.
			'inputs'                => [ 
				[ 
					// The size of the input field.
					'size'    => 1,

					// The type of input field.
					'type'    => 'select',

					// Whether the input field supports search.
					'search'  => true,

					// The name of the input field.
					'name'    => 'status',

					// The options available for selection.
					'options' => [ 
						// Option for logged-in users.
						[ 'value' => 'logged_in', 'label' => __( 'Logged In', 'swift-coupons-for-woocommerce' ) ],

						// Option for guest users.
						[ 'value' => 'guest', 'label' => __( 'Guest', 'swift-coupons-for-woocommerce' ) ],
					],
				],
			],
			'unlocked'              => true, // Indicates if the rule is locked.
			'lock_type'             => null, // Type of lock for the rule.
		];

		// Return the modified rules array.
		return $rules;
	}
}
