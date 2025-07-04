// Importing dependencies for group and switch properties
import GroupProps from "./GroupProps";
import SwitchProps from "./SwitchProps";

// Defining the default properties for rule inputs of type "text", "number", or "area"
type RuleInputDefaultProps = {
	size: number; // Size of the input field (e.g., column span in a grid layout)
	type: "text" | "number" | "area"; // Type of the input field
	name: string; // Unique name identifier for the input
	label?: string; // Optional label for the input field
	placeholder?: string; // Placeholder text for the input field
	value: string; // Default value for the input field
};

// Defining the properties for rule inputs of type "select"
type RuleInputSelectProps = {
	size: number; // Size of the input field
	type: "select"; // Type of the input field
	name: string; // Unique name identifier for the input
	label?: string; // Optional label for the input field
	options?: any[]; // Array of options for the select input
	value?: string | number | any[]; // Default value for the select input
	search?: boolean; // Whether the select input supports search functionality
	multiple?: boolean; // Whether multiple selections are allowed
	tags?: boolean; // Whether the input supports tag-like behavior
};

// Defining the properties for rule inputs of type "ajax-select"
type RuleInputSearchSelectProps = {
	size: number; // Size of the input field
	type: "ajax-select"; // Type of the input field
	name: string; // Unique name identifier for the input
	label?: string; // Optional label for the input field
	options?: any[]; // Array of options for the select input
	value?: string | number | any[]; // Default value for the select input
	multiple?: boolean; // Whether multiple selections are allowed
	tags?: boolean; // Whether the input supports tag-like behavior
	url: string; // URL for fetching options dynamically
	placeholder?: string; // Placeholder text for the input field
};

// Union type for all possible rule input properties
type RuleInputProps =
	| RuleInputDefaultProps
	| RuleInputSelectProps
	| RuleInputSearchSelectProps;

// Defining the properties for a rule
type RuleProps = {
	title: string; // Title of the rule
	description: string; // Description of the rule
	default_error_message?: string; // Default error message for the rule
	selected?: boolean; // Whether the rule is selected
	unlocked: boolean; // Whether the rule is locked
	lock_type: false | "premium" | "rating"; // Type of lock applied to the rule
	inputs?: RuleInputProps[]; // Array of input configurations for the rule
};

// Declaring the global variable for coupon settings
declare var swiftCouponSingle: {
	data: {
		qualifiers: (GroupProps | SwitchProps)[]; // Array of group or switch qualifiers
	};
	settings: {
		qualifiers: {
			rules: {
				[key: string]: {
					id: string; // Unique identifier for the rule
					category_id: string; // Identifier for the category the rule belongs to
					title: string; // Title of the rule
					description: string; // Description of the rule
					default_error_message: string; // Default error message for the rule
					inputs: RuleInputProps[]; // Array of input configurations for the rule
					unlocked: boolean; // Whether the rule is locked
					lock_type: false | "premium" | "rating"; // Type of lock applied to the rule
				};
			};
		};
	};
};

// Extracting rules from the global settings
const rules = swiftCouponSingle.settings?.qualifiers?.rules || {};

// Initializing an object to categorize rules by their category ID
let ruleCategories: {
	[key: string]: {
		title: string; // Title of the category
		rules: {
			id: string; // Unique identifier for the rule
			category_id: string; // Identifier for the category the rule belongs to
			title: string; // Title of the rule
			description: string; // Description of the rule
			default_error_message: string; // Default error message for the rule
			inputs: RuleInputProps[]; // Array of input configurations for the rule
		}[];
	};
} = {};

// Populating the ruleCategories object by iterating over the rules
Object.keys(rules).forEach((key) => {
	const rule = rules[key]; // Fetching the rule by its key
	const categoryId = rule.category_id; // Extracting the category ID of the rule

	// If the category does not exist in ruleCategories, initialize it
	if (!ruleCategories[categoryId]) {
		ruleCategories[categoryId] = {
			title: categoryId.replace("_", " "), // Formatting the category title
			rules: [],
		};
	}

	// Adding the rule to its respective category
	ruleCategories[categoryId].rules.push(rule);
});

// Exporting the rules and ruleCategories objects
export default rules;
export { ruleCategories };
