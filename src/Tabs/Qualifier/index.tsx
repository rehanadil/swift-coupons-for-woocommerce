// Importing necessary libraries and components
import React from "react";
import apiFetch from "@wordpress/api-fetch";
import Group from "./Group";
import GroupProps from "./GroupProps";
import SwitchProps from "./SwitchProps";
import Switch from "./Switch";
import { Button, Select, Switch as ToggleSwitch } from "antd";
import rules from "./rules";
import { conforms } from "lodash";
import { __ } from "@wordpress/i18n"; // Import WordPress translation function
import Title from "antd/es/typography/Title";
import Paragraph from "antd/es/typography/Paragraph";

// Type definition for Qualifier data
// Represents the structure of the qualifiers in the application
type Qualifier_Data = {
	enabled: boolean; // Whether qualifiers are enabled
	data: (GroupProps | SwitchProps)[]; // Array of groups and switches
};

// Declaring global variables provided by WordPress
// These are used to fetch and update data
declare var woocommerce_admin_meta_boxes: {
	post_id: number; // ID of the current post
};

declare var swiftCouponSingle: {
	data: {
		qualifiers: Qualifier_Data; // Qualifier data
	};
	settings: {
		qualifiers: {
			rules: {
				[key: string]: {
					id: string; // Rule ID
					category_id: string; // Category ID of the rule
					title: string; // Title of the rule
					description: string; // Description of the rule
					default_error_message: string; // Default error message for the rule
					inputs: {
						size: string; // Input size
						type: string; // Input type
						name?: string; // Optional input name
						value?: string; // Optional input value
						label?: string; // Optional input label
						placeholder?: string; // Optional input placeholder
					}[];
				};
			};
		};
	};
};

const Qualifiers: React.FC = () => {
	// State to manage whether qualifiers are enabled
	const [enabled, setEnabled] = React.useState<boolean>(
		swiftCouponSingle.data?.qualifiers?.enabled || false
	);
	// State to manage the list of groups and switches
	const [data, setData] = React.useState<(GroupProps | SwitchProps)[]>(
		swiftCouponSingle.data?.qualifiers?.data || []
	);

	// Effect to dispatch a custom event when data changes
	React.useEffect(() => {
		const event = new CustomEvent("swiftcou-coupon-data-changed", {
			detail: {
				type: "qualifiers",
				data: { enabled, data },
			},
		});
		window.dispatchEvent(event); // Dispatch the event
	}, [enabled, data]);

	// Function to toggle the enabled state of qualifiers
	const handleToggle = (checked: boolean) => {
		setEnabled(checked);
	};

	// Function to add a new group to the qualifiers
	const handleAddGroup = () => {
		setData((prev) => {
			return prev.length > 0
				? [
						...prev,
						{ type: "switch", state: "OR" }, // Add a switch between groups
						{
							type: "group",
							rules: [],
							settings: { error_message: "" },
						},
					]
				: [
						{
							type: "group",
							rules: [],
							settings: { error_message: "" },
						},
					];
		});
	};

	// Function to update a group's data
	const handleGroupUpdate = (
		groupIndex: number,
		key: string | number,
		value: any
	) => {
		setData((prev) => {
			const newData = [...prev];

			const group = newData[groupIndex];

			if (group.type !== "group") {
				return newData; // Ensure the item is a group
			}

			if (key === "error_message") {
				group.settings.error_message = value; // Update the error message
			}

			return newData;
		});
	};

	// Function to add a rule to a group
	const handleRuleAdd = (rule: string, groupIndex: number) => {
		setData((prev) => {
			const newData = [...prev];

			if (!newData[groupIndex]) {
				return newData; // Ensure the group exists
			}

			const group = newData[groupIndex];

			if (group.type !== "group") {
				return newData; // Ensure the item is a group
			}

			if (group.rules.length > 0) {
				group.rules.push({
					type: "switch",
					state: "AND", // Add a switch between rules
				});
			}

			group.rules.push({
				type: "rule",
				id: rule,
				data: {},
				settings: {
					error_message: rules[rule].default_error_message || "",
				},
			});
			newData[groupIndex] = group;
			return newData;
		});
	};

	// Function to remove a group
	const handleGroupRemove = (index: number) => {
		setData((prev) => {
			return index === data.length - 1
				? prev.filter((_, i) => i !== index - 1 && i !== index) // Remove the group and its preceding switch
				: prev.filter((_, i) => i !== index && i !== index + 1); // Remove the group and its following switch
		});
	};

	// Function to update a rule within a group
	const handleRuleUpdate = (
		groupIndex: number,
		ruleIndex: number,
		key: string | number,
		value: any
	) => {
		setData((prev) => {
			const newData = [...prev];

			const group = newData[groupIndex];

			if (group.type !== "group") {
				return newData; // Ensure the item is a group
			}

			if (!group.rules[ruleIndex]) {
				return newData; // Ensure the rule exists
			}

			const rule = group.rules[ruleIndex];

			if (rule.type !== "rule") {
				return newData; // Ensure the item is a rule
			}

			if (key === "settings") {
				rule.settings = value; // Update rule settings
			} else {
				rule.data[key] = value; // Update rule data
			}

			return newData;
		});
	};

	// Function to remove a rule from a group
	const handleRuleRemove = (groupIndex: number, ruleIndex: number) => {
		setData((prev) => {
			const newData = [...prev];

			const group = newData[groupIndex];

			if (group.type !== "group") {
				return newData; // Ensure the item is a group
			}

			if (!group.rules[ruleIndex]) {
				return newData; // Ensure the rule exists
			}

			if (group.rules.length === 1) {
				group.rules.splice(ruleIndex, 1); // Remove the only rule
			} else if (ruleIndex === group.rules.length - 1) {
				group.rules.splice(ruleIndex - 1, 2); // Remove the rule and its preceding switch
			} else {
				group.rules.splice(ruleIndex, 2); // Remove the rule and its following switch
			}

			newData[groupIndex] = group;
			return newData;
		});
	};

	// Function to handle switch state changes within a group
	const handleGroupSwitch = (
		groupIndex: number,
		switchIndex: number,
		state: "AND" | "OR"
	) => {
		setData((prev) => {
			const newData = [...prev];

			const group = newData[groupIndex];

			if (group.type !== "group" || group.rules.length === 0) {
				return newData; // Ensure the item is a group with rules
			}

			const _switch = group.rules[switchIndex];

			if (_switch.type !== "switch") {
				return newData; // Ensure the item is a switch
			}

			if (_switch.state === state) {
				return newData; // No change needed if the state is the same
			}

			_switch.state = state; // Update the switch state
			group.rules[switchIndex] = _switch;
			newData[groupIndex] = group;
			return newData;
		});
	};

	// Function to handle switch state changes outside a group
	const handleSwitch = (index: number, state: "AND" | "OR") => {
		setData((prev) => {
			const newData = [...prev];

			const _switch = newData[index];

			if (_switch.type !== "switch") {
				return newData; // Ensure the item is a switch
			}

			if (_switch.state === state) {
				return newData; // No change needed if the state is the same
			}

			_switch.state = state; // Update the switch state
			newData[index] = _switch;
			return newData;
		});
	};

	return (
		<>
			{/* Header section with title and description */}
			<div className="tw-px-4 tw-flex tw-flex-col">
				<Title level={3} className="tw-mt-4">
					{__("Cart Qualifiers", "swift-coupons")}
				</Title>
				<Paragraph>
					{__(
						"Cart Qualifiers are a set of rules or conditions that must be met for a coupon to be valid and applicable. These qualifiers allow you to define specific criteria, such as cart contents, purchase amounts, or customer attributes, ensuring that the coupon is used in the right scenarios. By tailoring these qualifiers, you can create targeted promotions that align with your business goals and enhance customer satisfaction.",
						"swift-coupons"
					)}
				</Paragraph>
			</div>

			{/* Toggle switch to enable or disable qualifiers */}
			<div className="tw-px-4 tw-mt-2 tw-flex tw-items-center">
				<ToggleSwitch
					checked={enabled}
					onChange={handleToggle}
					className="tw-mr-2"
				/>
				<span className="tw-text-sm tw-font-semibold">
					{__("Enable Qualifiers", "swift-coupons")}
				</span>
			</div>

			{/* Render groups and switches */}
			{data.map((each, index) => {
				const nextGroup = data[index + 1] || false; // Reference to the next group or switch

				switch (each.type) {
					case "group":
						return (
							<Group
								key={index}
								index={index}
								data={each}
								onUpdate={handleGroupUpdate}
								onRemove={handleGroupRemove}
								onRuleAdd={handleRuleAdd}
								onRuleUpdate={handleRuleUpdate}
								onRuleRemove={handleRuleRemove}
								onSwitch={(
									ruleIndex: number,
									state: "AND" | "OR"
								) => {
									handleGroupSwitch(index, ruleIndex, state);
								}}
								next={nextGroup}
								className={
									enabled
										? ""
										: "tw-opacity-50 tw-pointer-events-none"
								}
							/>
						);

					case "switch":
						return (
							<Switch
								key={index}
								state={each.state}
								placement="outside_group"
								onChange={(state) => handleSwitch(index, state)}
							/>
						);
				}
			})}

			{/* Button to add a new group */}
			{enabled && (
				<Button
					type="dashed"
					className="tw-m-4"
					onClick={() => handleAddGroup()}
				>
					{__("Add New Group", "swift-coupons")}
				</Button>
			)}
		</>
	);
};

export default Qualifiers;
