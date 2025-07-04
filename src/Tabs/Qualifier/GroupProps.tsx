// Importing dependencies for defining GroupProps
import SwitchProps from "./SwitchProps";
import RuleProps from "./RuleProps";

// Defining the GroupProps type
// Represents the structure of a group in the application
// A group contains rules and settings
// - type: Specifies that this is a "group"
// - rules: An array of rules or switches that belong to the group
// - settings: Configuration settings for the group, such as error messages
type GroupProps = {
	type: "group"; // Indicates the type of this object is a group
	rules: (RuleProps | SwitchProps)[]; // Array of rules or switches
	settings: {
		error_message: string; // Custom error message for the group
	};
};

export default GroupProps;
