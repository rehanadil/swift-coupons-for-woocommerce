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
		$user_roles = $this->get_data( 'user_roles' );

		// Get the current user ID
		$user_id = get_current_user_id();

		// Create a WP_User object for the current user
		$user = new \WP_User( $user_id );

		// Get the roles of the current user
		$user_roles = $user->roles;

		// Check if the current user roles intersect with the rule-provided user roles
		$is = ! empty( array_intersect( $user->roles, $user_roles ) );

		// Return the comparison result based on the logic
		switch ( $logic )
		{
			case 'has': // Logic to check if the user has the roles
				return $is;
			case 'not_has': // Logic to check if the user does not have the roles
				return ! $is;
			default: // Default case if logic is invalid
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
					'size'    => 1 / 3,
					'type'    => 'select',
					'name'    => 'logic',
					'options' => $this->get_logic_options( 'has' ),
				],
				[ 
					// Input for selecting user roles
					'size'     => 2 / 3,
					'type'     => 'ajax-select',
					'name'     => 'user_roles',
					'label'    => __( 'User Roles', 'swift-coupons-for-woocommerce' ),
					'search'   => true,
					'multiple' => true,
					'url'      => '/swift-coupons/v1/users/roles?query={query}',
				],
			],
			'unlocked'              => true, // Indicates if the rule is locked.
			'lock_type'             => null, // Type of lock for the rule.
		];

		// Return the modified rules array
		return $rules;
	}
}
