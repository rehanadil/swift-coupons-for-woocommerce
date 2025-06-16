<?php
namespace Sreshto\SwiftCoupon\Qualifier\Rule;

/**
 * Abstract base class for defining rules in Swift Coupons.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author Rehan Adil
 */
abstract class Rule_Base
{
	// Private property to store data.
	private $data;

	// Private array to store variables.
	private array $variables = [];

	/**
	 * Constructor to initialize the rule base.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @return void
	 */
	public function __construct()
	{
		// Add a filter to register the rule.
		add_filter( 'swiftcou_rules', [ $this, 'register_rule' ] );
	}

	/**
	 * Registers the rule.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param array $rules The array of rules.
	 * @return array The array of rules to be registered.
	 */
	public function register_rule( $rules )
	{
		// Return the rules array.
		return $rules;
	}

	/**
	 * Abstract method to check if the rule matches.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @return bool True if the rule matches, false otherwise.
	 */
	abstract public function match();

	/**
	 * Parses the message with variables.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $message The message to parse.
	 * @return string The parsed message.
	 */
	public function parse_message_with_variables( $message )
	{
		// Get the variables.
		$variables = $this->variables;

		// Loop through each variable and replace it in the message.
		foreach ( $variables as $key => $value )
		{
			$message = $this->parse_vars( $key, $value, $message );
		}

		// Return the parsed message.
		return $message;
	}

	/**
	 * Gets the data or a specific key from the data.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string|null $key The key to retrieve.
	 * @return mixed The data or specific key value.
	 */
	public function get_data( $key = null )
	{
		// Return all data if no key is provided.
		if ( null === $key )
			return $this->data;

		// Return the specific key value or null if not found.
		return $this->data[ $key ] ?? null;
	}

	/**
	 * Sets the data and updates variables.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param mixed $data The data to set.
	 * @return void
	 */
	public function set_data( $data )
	{
		// Set the data.
		$this->data = $data;

		// Update the variables.
		$this->update_variables();
	}

	/**
	 * Sets a specific variable.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $key The variable key.
	 * @param mixed $value The variable value.
	 * @return void
	 */
	public function set_variable( $key, $value )
	{
		// Set the variable in the variables array.
		$this->variables[ $key ] = $value;
	}

	/**
	 * Updates the variables based on the data.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @return void
	 */
	private function update_variables()
	{
		// Loop through the data and update variables.
		foreach ( $this->data as $key => $value )
		{
			// Handle the 'logic' key separately.
			if ( 'logic' === $key )
			{
				$this->set_variable( 'logic', $this->logic_text( $value ) );
				continue;
			}

			// Set the variable for other keys.
			$this->set_variable( $key, $value );
		}
	}

	/**
	 * Converts a logic operator to its textual representation.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $operator The logic operator.
	 * @return string The textual representation of the operator.
	 */
	public function logic_text( $operator )
	{
		// Return the textual representation based on the operator.
		switch ( $operator )
		{
			case 'mt':
				return __( 'more than', 'swift-coupons' );

			case 'lt':
				return __( 'less than', 'swift-coupons' );

			case 'ab':
				return __( 'anything but', 'swift-coupons' );

			case 'eq':
				return __( 'exactly', 'swift-coupons' );
		}

		// Return an empty string if no match is found.
		return '';
	}

	/**
	 * Compares two values based on the operator.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $operator The comparison operator.
	 * @param mixed $var1 The first value.
	 * @param mixed $var2 The second value.
	 * @return bool True if the comparison is valid, false otherwise.
	 */
	public function compare( $operator, $var1, $var2 )
	{
		// Perform the comparison based on the operator.
		switch ( $operator )
		{
			case 'mt':
				if ( $var1 > $var2 )
					return true;
				break;

			case 'lt':
				if ( $var1 < $var2 )
					return true;
				break;

			case 'ab':
				if ( $var1 != $var2 )
					return true;
				break;

			case 'eq':
				if ( $var1 == $var2 )
					return true;
				break;
		}

		// Return false if no match is found.
		return false;
	}

	/**
	 * Checks if a logic condition exists.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $logic The logic condition.
	 * @param bool $bool The boolean value to check.
	 * @return bool True if the condition is met, false otherwise.
	 */
	public function exists( $logic, $bool )
	{
		// Check if the logic condition exists.
		if ( $logic === 'exists' && $bool )
			return true;

		// Check if the logic condition does not exist.
		if ( $logic === 'not_exists' && ! $bool )
			return true;

		// Return false if no match is found.
		return false;
	}

	/**
	 * Checks if a value exists in a haystack based on logic.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $logic The logic condition.
	 * @param mixed $needle The value to search for.
	 * @param mixed $haystack The haystack to search in.
	 * @return bool True if the condition is met, false otherwise.
	 */
	public function has( $logic, $needle, $haystack )
	{
		// Initialize the has variable.
		$has = false;

		// Check if the needle exists in the haystack array.
		if ( is_array( $haystack ) && in_array( $needle, $haystack ) )
			$has = true;

		// Check if the needle exists in the haystack string.
		if ( strpos( $haystack, $needle ) !== false )
			$has = true;

		// Return the result based on the logic condition.
		return $logic === 'has' ? $has : ! $has;
	}

	/**
	 * Gets the available logic options.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @return array The available logic options.
	 */
	public function get_available_logic_options()
	{
		// Apply filters to get the available logic options.
		return apply_filters( 'swiftcou_rule_options', [ 
			'compare' => [ 
				'label'   => __( 'Compare', 'swift-coupons' ),
				'options' => [ 
					[ 'value' => 'mt', 'label' => __( 'More Than', 'swift-coupons' ) ],
					[ 'value' => 'lt', 'label' => __( 'Less Than', 'swift-coupons' ) ],
					[ 'value' => 'ab', 'label' => __( 'Anything But', 'swift-coupons' ) ],
					[ 'value' => 'eq', 'label' => __( 'Exactly', 'swift-coupons' ) ],
				],
			],
			'exists'  => [ 
				'label'   => __( 'Exists', 'swift-coupons' ),
				'options' => [ 
					[ 'value' => 'exists', 'label' => __( 'Exists', 'swift-coupons' ) ],
					[ 'value' => 'not_exists', 'label' => __( 'Not Exists', 'swift-coupons' ) ],
				],
			],
			'has'     => [ 
				'label'   => __( 'Has', 'swift-coupons' ),
				'options' => [ 
					[ 'value' => 'has', 'label' => __( 'Has', 'swift-coupons' ) ],
					[ 'value' => 'not_has', 'label' => __( 'Not Has', 'swift-coupons' ) ],
				],
			],
		] );
	}

	/**
	 * Gets the logic options for specific types.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string ...$types The types to retrieve options for.
	 * @return array The logic options for the specified types.
	 */
	public function get_logic_options( ...$types )
	{
		// Get the available options.
		$available_options = $this->get_available_logic_options();

		// Initialize the options array.
		$options = [];

		// Loop through the types.
		foreach ( $types as $type )
		{
			// Check if the type exists.
			if ( ! isset( $available_options[ $type ] ) )
				continue;

			// Add the type options to the options array.
			$options[] = $available_options[ $type ];
		}

		// Return the options.
		return $options;
	}

	/**
	 * Gets the logic type for a specific logic value.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $logic The logic value.
	 * @return string|null The logic type or null if not found.
	 */
	public function get_logic_type( $logic )
	{
		// Get the available options.
		$available_options = $this->get_available_logic_options();

		// Loop through the available options.
		foreach ( $available_options as $type => $options )
		{
			// Loop through the options.
			foreach ( $options[ 'options' ] as $option )
			{
				// Check if the value matches.
				if ( $option[ 'value' ] === $logic )
					return $type;
			}
		}

		// Return null if no match is found.
		return null;
	}

	/**
	 * Parses variables in a message.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @author Rehan Adil
	 * @param string $key The variable key.
	 * @param mixed $value The variable value.
	 * @param string &$message The message to parse.
	 * @return string The parsed message.
	 */
	private function parse_vars( $key, $value, &$message )
	{
		// Check if the value is an array.
		if ( is_array( $value ) )
		{
			// Loop through the array and parse variables recursively.
			foreach ( $value as $k => $v )
				$message = $this->parse_vars( $key . '.' . $k, $v, $message );
		}
		else
		{
			// Replace the variable in the message.
			$message = str_replace( '{' . $key . '}', $value, $message );
		}

		// Return the parsed message.
		return $message;
	}
}
