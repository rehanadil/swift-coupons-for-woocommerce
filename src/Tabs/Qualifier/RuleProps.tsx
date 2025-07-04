// Defining the RuleProps type
// This type represents the structure of a rule object used in the application
type RuleProps = {
	// Specifies the type of the object, which is always "rule"
	type: "rule";
	// Unique identifier for the rule
	id: string;
	// Data associated with the rule, can be of any type
	data: any;
	// Settings for the rule, including error message configuration
	settings: {
		// Custom error message to display when the rule fails
		error_message: string;
	};
};

export default RuleProps;
