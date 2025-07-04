<?php
namespace Sreshto\SwiftCoupons;

use WC_Coupon;

/**
 * Class Qualifier
 *
 * Handles the evaluation of coupon qualifiers based on defined rules and groups.
 * This class integrates with WooCommerce to validate coupons and ensures that
 * the qualifiers are met before a coupon can be applied.
 *
 * @package Sreshto\SwiftCoupons
 * @author Rehan Adil
 */
class Qualifier
{
	// Define constants for meta keys
	const KEY_ENABLED = 'swiftcoupons_qualifiers_enabled';
	const KEY_DATA    = 'swiftcoupons_qualifiers';

	// Declare private properties
	private array  $groups        = [];
	private string $error_message = '';
	private array  $rules         = [];
	public static  $instance;

	/**
	 * Constructor to initialize the class.
	 *
	 * @return void
	 */
	private function __construct()
	{
		// Load rules
		$this->load_rules();

		// Add WooCommerce filter for coupon validation
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'run' ), 12, 2 );
	}

	/**
	 * Get the singleton instance of the class.
	 *
	 * @return Qualifier
	 * @author Rehan Adil
	 */
	public static function instance()
	{
		// Check if instance is null
		if ( is_null( self::$instance ) )
		{
			// Create new instance
			self::$instance = new self();
		}

		// Return the instance
		return self::$instance;
	}

	/**
	 * Evaluate a group of rules.
	 *
	 * @param array $group The group of rules to evaluate.
	 * @return bool True if the group passes, false otherwise.
	 * @author Rehan Adil
	 */
	public function evaluate_group( $group )
	{
		// Initialize evaluation and switch counter
		$evaluation         = true;
		$number_of_switches = 0;

		// Iterate through group rules
		foreach ( $group[ 'rules' ] as $i => $rule )
		{
			// Check if the rule is a switch
			if ( ! $this->is_switch( 'rule', $rule ) )
				continue;

			// Get the previous rule and its evaluation
			$prev_rule       = $group[ 'rules' ][ $i - 1 ];
			$prev_evaluation = $this->evaluate_rule( $prev_rule );

			// Handle OR state
			if ( $rule[ 'state' ] === 'OR' && $prev_evaluation )
			{
				$evaluation = true;
				$number_of_switches++;
				continue;
			}

			// Get the next rule and its evaluation
			$next_rule       = $group[ 'rules' ][ $i + 1 ];
			$next_evaluation = $this->evaluate_rule( $next_rule );

			// Combine evaluations
			$evaluation = $prev_evaluation && $next_evaluation;
			$number_of_switches++;
		}

		// Handle case with no switches
		if ( 0 === $number_of_switches )
		{
			$rule       = $group[ 'rules' ][ 0 ];
			$evaluation = $this->evaluate_rule( $rule );
		}

		// Return the evaluation result
		return $evaluation;
	}

	/**
	 * Evaluate a single rule.
	 *
	 * @param array $rule The rule to evaluate.
	 * @return bool True if the rule passes, false otherwise.
	 * @author Rehan Adil
	 */
	public function evaluate_rule( $rule )
	{
		// Extract rule details
		$rule_id   = $rule[ 'id' ];
		$rule_data = $rule[ 'data' ];

		// Get rule settings and error message
		$rule_settings = $rule[ 'settings' ];
		$error_message = isset( $rule_settings[ 'error_message' ] ) ? $rule_settings[ 'error_message' ] : '';

		// Check if rule exists in loaded rules
		if ( array_key_exists( $rule_id, $this->rules ) )
		{
			// Get rule instance and set data
			$rule_class    = $this->rules[ $rule_id ];
			$rule_instance = is_object( $rule_class ) ? $rule_class : new $rule_class();
			$rule_instance->set_data( $rule_data );

			// Check if rule matches
			if ( $rule_instance->match() )
				return true;
			else
			{
				// Set error message and return false
				$this->error_message = $rule_instance->parse_message_with_variables( $error_message );
				return false;
			}
		}

		// Apply filter for rule result
		return apply_filters( 'swift-coupons/rule-result', false, $rule );
	}

	/**
	 * Get data associated with a coupon.
	 *
	 * @param WC_Coupon|string $coupon The coupon object or code.
	 * @return array The data associated with the coupon.
	 * @author Rehan Adil
	 */
	private function get_data( $coupon )
	{
		// Get coupon ID and meta data
		$id   = wc_get_coupon_id_by_code( $coupon instanceof WC_Coupon ? $coupon->get_code() : $coupon );
		$data = get_post_meta( $id, self::KEY_DATA, true );
		$data = empty( $data ) || ! is_array( $data ) ? [] : $data;

		// Return data array
		return isset( $data[ 'data' ] ) && is_array( $data[ 'data' ] ) ? $data[ 'data' ] : [];
	}

	/**
	 * Check if qualifiers are enabled for a coupon.
	 *
	 * @param WC_Coupon|string $coupon The coupon object or code.
	 * @return bool True if qualifiers are enabled, false otherwise.
	 * @author Rehan Adil
	 */
	private function is_enabled( $coupon )
	{
		// Get coupon ID and meta data
		$id   = wc_get_coupon_id_by_code( $coupon instanceof WC_Coupon ? $coupon->get_code() : $coupon );
		$data = get_post_meta( $id, self::KEY_DATA, true );
		$data = empty( $data ) || ! is_array( $data ) ? [] : $data;

		// Return enabled status
		return isset( $data[ 'enabled' ] ) && boolval( $data[ 'enabled' ] );
	}

	/**
	 * Check if a data type is a switch.
	 *
	 * @param string $type The type of data ('group' or 'rule').
	 * @param array $data The data to check.
	 * @return bool True if the data is a switch, false otherwise.
	 * @author Rehan Adil
	 */
	private function is_switch( $type, $data )
	{
		// Check for group type
		if ( 'group' === $type )
			return ( $data[ 'type' ] === 'switch' ) ? true : false;
		// Check for rule type
		elseif ( 'rule' === $type )
			return ( $data[ 'type' ] === 'switch' ) ? true : false;

		// Default to false
		return false;
	}

	/**
	 * Main method to run coupon validation.
	 *
	 * @param bool $is_valid Whether the coupon is valid so far.
	 * @param WC_Coupon|string $coupon The coupon object or code.
	 * @return bool True if the coupon is valid, false otherwise.
	 * @throws \Exception If the coupon fails validation.
	 * @author Rehan Adil
	 */
	public function run( $is_valid, $coupon )
	{
		// Return false if coupon is already invalid
		if ( ! $is_valid )
			return false;

		// Check if qualifiers are enabled
		if ( ! $this->is_enabled( $coupon ) )
			return $is_valid;

		// Get groups of rules
		$groups = $this->get_data( $coupon );

		// Return if no groups are found
		if ( empty( $groups ) )
			return $is_valid;

		// Get the number of groups
		$number_of_groups = count( $groups );

		// Handle cases based on the number of groups
		if ( 0 === $number_of_groups )
		{
			return $is_valid;
		}
		elseif ( 1 === $number_of_groups )
		{
			$evaluation = $this->evaluate_group( $groups[ 0 ] );
		}
		else
		{
			// Iterate through groups
			foreach ( $groups as $i => $group )
			{
				// Check if the group is a switch
				if ( ! $this->is_switch( 'group', $group ) )
					continue;

				// Get the previous group and its evaluation
				$prev_group      = $groups[ $i - 1 ];
				$prev_evaluation = isset( $evaluation ) ? $evaluation : $this->evaluate_group( $prev_group );

				// Handle OR state
				if ( $group[ 'state' ] === 'OR' && $prev_evaluation )
				{
					$evaluation = true;
					continue;
				}

				// Get the next group and its evaluation
				$next_group      = $groups[ $i + 1 ];
				$next_evaluation = $this->evaluate_group( $next_group );

				// Combine evaluations
				$evaluation = $prev_evaluation && $next_evaluation;
			}
		}

		// Throw exception if evaluation fails
		if ( ! $evaluation )
		{
			throw new \Exception( $this->error_message ? esc_html( $this->error_message ) : esc_html__( 'You are not qualified to use this coupon.', 'swift-coupons-for-woocommerce' ) );
		}

		// Return evaluation result
		return $evaluation;
	}

	/**
	 * Load rule classes from the specified directory.
	 *
	 * @return void
	 * @author Rehan Adil
	 */
	public function load_rules()
	{
		$default_rules = apply_filters( 'swift-coupons/qualifier-default-rules', [ 
			'Cart_Item_Meta'                      => '\Sreshto\SwiftCoupons\Qualifier\Rule\Cart_Item_Meta',
			'Cart_Quantity'                       => new \Sreshto\SwiftCoupons\Qualifier\Rule\Cart_Quantity,
			'Cart_Subtotal'                       => new \Sreshto\SwiftCoupons\Qualifier\Rule\Cart_Subtotal,
			'Cart_Weight'                         => '\Sreshto\SwiftCoupons\Qualifier\Rule\Cart_Weight',
			'Coupons_Applied_In_Cart'             => '\Sreshto\SwiftCoupons\Qualifier\Rule\Coupons_Applied_In_Cart',
			'Category_Quantity_In_Cart'           => new \Sreshto\SwiftCoupons\Qualifier\Rule\Category_Quantity_In_Cart,
			'Customer_Has_Ordered_Product_Before' => '\Sreshto\SwiftCoupons\Qualifier\Rule\Customer_Has_Ordered_Product_Before',
			'Customer_Logged_Status'              => new \Sreshto\SwiftCoupons\Qualifier\Rule\Customer_Logged_Status,
			'Customer_Meta'                       => '\Sreshto\SwiftCoupons\Qualifier\Rule\Customer_Meta',
			'Customer_Order_Count'                => '\Sreshto\SwiftCoupons\Qualifier\Rule\Customer_Order_Count',
			'Customer_Total_Spent'                => '\Sreshto\SwiftCoupons\Qualifier\Rule\Customer_Total_Spent',
			'Customer_User_Roles'                 => new \Sreshto\SwiftCoupons\Qualifier\Rule\Customer_User_Roles,
			'Product_Meta'                        => '\Sreshto\SwiftCoupons\Qualifier\Rule\Product_Meta',
			'Product_Quantity_In_Cart'            => '\Sreshto\SwiftCoupons\Qualifier\Rule\Product_Quantity_In_Cart',
		] );

		foreach ( $default_rules as $rule_id => $rule_class )
		{
			// If the rule class is already an object, just assign it
			if ( is_object( $rule_class ) )
			{
				$this->rules[ $rule_id ] = $rule_class;
				continue;
			}

			if ( ! is_string( $rule_class ) || empty( $rule_class ) )
			{
				continue;
			}

			// Add the rule class to the rules array
			$this->rules[ $rule_id ] = new $rule_class;
		}

		// Apply filter to rule classes
		$this->rules = apply_filters( 'swift-coupons/qualifier-rules', $this->rules );
	}
}
