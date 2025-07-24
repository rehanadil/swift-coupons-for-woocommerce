<?php
namespace Sreshto\SwiftCoupons\Qualifier\Rule;

/**
 * Class Customer_User_Roles
 *
 * This class defines a rule to check if a customer's user roles match the specified criteria.
 *
 * @since 1.0
 * @version 1.0
 * @author Rehan Adil
 * @package Sreshto\SwiftCoupons\Qualifier\Rule
 */
class Customer_User_Roles extends Rule_Base
{
	/**
	 * Checks if the customer user role matches the rule
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 *
	 * @return bool The result of the match
	 */
	public function match()
	{
		// Get the logic data from the rule
		$logic = $this->get_data( 'logic' );

		// Get the user roles data from the rule
		$selected_roles       = $this->get_data( 'user_roles' );
		$selected_role_values = is_array( $selected_roles ) ? array_map( function ($role)
		{
			return is_array( $role ) ? $role[ 'value' ] : $role;
		}, $selected_roles ) : [];

		$match = $this->get_data( 'match' );

		// Get the current user ID
		$user_id = get_current_user_id();

		// Create a WP_User object for the current user
		$user = new \WP_User( $user_id );

		// Determine match type: 'any' or 'all'
		if ( $match === 'any' )
			$is = ! empty( array_intersect( $user->roles, $selected_role_values ) );
		elseif ( $match === 'all' )
			$is = ! array_diff( $selected_role_values, $user->roles );
		else
			$is = false;

		// Return the comparison result based on the logic
		switch ( $logic )
		{
			case 'has':
				return $is;
			case 'not_has':
				return ! $is;
			default:
				return false;
		}
	}

	/**
	 * Registers the rule
	 *
	 * Adds the Customer_User_Roles rule to the list of available rules.
	 *
	 * @since 1.0
	 * @version 1.0
	 * @author Rehan Adil
	 * @param array $rules The array of rules
	 * @return array The modified array with the new rule to be registered
	 */
	public function register_rule( $rules )
	{
		// Add the Customer_User_Roles rule to the rules array
		$rules[ 'Customer_User_Roles' ] = [ 
			// The ID of the rule
			'id'                    => 'Customer_User_Roles',
			// The category of the rule
			'category_id'           => 'Customer',
			// The title of the rule
			'title'                 => __( 'Customer User Roles', 'swift-coupons-for-woocommerce' ),
			// The description of the rule
			'description'           => __( 'Filter coupon validity by user roles. Can be used to either include or exclude certain user roles from using this coupon.', 'swift-coupons-for-woocommerce' ),
			// The error message to be displayed if the rule does not match
			'default_error_message' => __( 'This coupon is not valid for your user role.', 'swift-coupons-for-woocommerce' ),
			// The inputs for the rule
			'inputs'                => [ 
				[ 
					// Input for selecting logic (has or not_has)
					'size'    => 1 / 6,
					'type'    => 'select',
					'name'    => 'logic',
					'options' => $this->get_logic_options( 'has' ),
				],
				[ 
					// Input for selecting user roles
					'size'     => 4 / 6,
					'type'     => 'ajax-select',
					'name'     => 'user_roles',
					'label'    => __( 'User Roles', 'swift-coupons-for-woocommerce' ),
					'search'   => true,
					'multiple' => true,
					'url'      => '/swift-coupons/v1/users/roles?query={query}',
				],
				[ 
					// Define the size of the input field.
					'size'    => 1 / 6,
					// Define the input type as a select dropdown.
					'type'    => 'select',
					// Define the name of the input field.
					'name'    => 'match',
					// Provide options for the select dropdown.
					'options' => [ 
						[ 'value' => 'any', 'label' => __( 'Match Any', 'swift-coupons-premium' ) ],
						[ 'value' => 'all', 'label' => __( 'Match All', 'swift-coupons-premium' ) ],
					],
				],
			],
			'unlocked'              => true, // Indicates if the rule is locked.
			'lock_type'             => null, // Type of lock for the rule.
		];

		// Return the modified rules array
		return $rules;
	}
}
