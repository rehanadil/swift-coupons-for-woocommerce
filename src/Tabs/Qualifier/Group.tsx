// Importing necessary libraries and components
import rules, { ruleCategories } from "./rules";
import Rule from "./Rule";
import Switch from "./Switch";
import React, { useEffect, useRef } from "react";
import { Button, Flex, Input, Modal, Select, Tooltip } from "antd";
import {
	LockFilled,
	LockOutlined,
	QuestionCircleOutlined,
	SettingOutlined,
} from "@ant-design/icons";
import FloatInput from "../../Components/FloatInput";
import { __ } from "@wordpress/i18n"; // Import WordPress translation function
import BetterSelect from "../../Components/BetterSelect";

// Props type definition for the Group component
type Props = {
	index: number; // Index of the group
	data: {
		rules: any[]; // Array of rules in the group
		settings: {
			error_message: string; // Custom error message for the group
		};
	};
	onUpdate: (groupIndex: number, key: string | number, value: any) => void; // Callback to update group data
	onRemove: Function; // Callback to remove the group
	onRuleAdd: (rule: string, groupIndex: number) => void; // Callback to add a rule to the group
	onRuleUpdate: (
		groupIndex: number,
		ruleIndex: number,
		key: string | number,
		value: any
	) => void; // Callback to update a rule in the group
	onRuleRemove: (groupIndex: number, ruleIndex: number) => void; // Callback to remove a rule from the group
	onSwitch: (index: number, o: "AND" | "OR") => void; // Callback to handle switch state changes
	next: any; // Reference to the next group or rule
	className?: string; // Optional class name for styling
};

const Group = ({
	index,
	data,
	onUpdate,
	onRemove,
	onRuleAdd,
	onRuleUpdate,
	onRuleRemove,
	onSwitch,
	next,
	className = "",
}: Props) => {
	// State to manage the visibility of the settings dialog
	const [showDialog, setShowDialog] = React.useState<boolean>(false);
	// State to confirm deletion of the group
	const [confirmDelete, setConfirmDelete] = React.useState<boolean>(false);

	// Reference to the error message input field
	const errorMessageInputRef = useRef<HTMLTextAreaElement>(null);

	// Reset confirmDelete state when the dialog visibility changes
	useEffect(() => {
		setConfirmDelete(false);
	}, [showDialog]);

	// Function to close the settings dialog
	const handleDialogClose = () => {
		setShowDialog(false);
	};

	// Extracting rules from the group data
	const rules = data.rules;

	return (
		<>
			{/* Main container for the group */}
			<Flex
				gap="middle"
				vertical
				className={`tw-bg-zinc-100 tw-m-4 tw-p-3 tw-rounded-md tw-shadow-md tw-relative tw-group/group ${className}`}
			>
				{/* Render each rule or switch in the group */}
				{rules.map((rule, ruleIndex) => {
					const nextRule = rules[ruleIndex + 1] || false; // Reference to the next rule

					switch (rule.type) {
						case "rule":
							return (
								<Rule
									index={ruleIndex}
									groupIndex={index}
									data={rule}
									onChange={(key, value) =>
										onRuleUpdate(
											index,
											ruleIndex,
											key,
											value
										)
									}
									onRemove={onRuleRemove}
									className={
										rules.length === 1
											? "tw-mx-1 tw-my-4"
											: ruleIndex === 0
											? "tw-mx-4 tw-mt-4"
											: "tw-mx-4"
									}
									next={nextRule}
								></Rule>
							);

						case "switch":
							return (
								<Switch
									key={index + "switch" + ruleIndex}
									state={rule.state}
									placement="inside_group"
									onChange={(state) =>
										onSwitch(ruleIndex, state)
									}
								/>
							);
					}
				})}

				{/* Rule picker to add new rules */}
				<div>
					<RulePicker
						groupIndex={index}
						onChoose={onRuleAdd}
						className="tw-my-3"
					/>
				</div>

				{/* Settings button */}
				<div
					className="tw-absolute tw--top-1 tw--right-1 tw-opacity-0 group-hover/group:tw-opacity-100 tw-transition-opacity tw-duration-200 tw-cursor-pointer"
					onClick={() => setShowDialog(true)}
				>
					<SettingOutlined className="tw-text-xl" />
				</div>
			</Flex>

			{/* Modal for group settings */}
			<Modal
				title={__("Group Settings", "swift-coupons")}
				centered
				open={showDialog}
				onOk={() => setShowDialog(false)}
				onCancel={() => handleDialogClose()}
				footer={[
					/* Delete button with confirmation */
					<Button
						key={index + "delete"}
						danger={!confirmDelete}
						type={confirmDelete ? "primary" : "default"}
						onClick={() => {
							if (confirmDelete) {
								onRemove(index);
								setShowDialog(false);
							} else {
								setConfirmDelete(true);
							}
						}}
						className={`tw-mr-4 tw-float-start ${
							confirmDelete ? "tw-bg-red-500 tw-text-white" : ""
						}`}
					>
						{confirmDelete
							? __("Confirm Delete", "swift-coupons")
							: __("Delete", "swift-coupons")}
					</Button>,
					/* Cancel button */
					<Button
						key={index + "cancel"}
						type={confirmDelete ? "dashed" : "default"}
						className={`tw-mx-1 ${
							confirmDelete
								? "tw-text-green-500 tw-border-green-500"
								: ""
						}`}
						onClick={() => {
							if (confirmDelete) {
								setConfirmDelete(false);
							} else {
								handleDialogClose();
							}
						}}
					>
						{confirmDelete
							? __("Cancel", "swift-coupons")
							: __("Close", "swift-coupons")}
					</Button>,
					/* Save button */
					<Button
						key={index + "submit"}
						type="primary"
						className="tw-mx-1"
						disabled={confirmDelete}
						onClick={() => handleDialogClose()}
					>
						{__("Save", "swift-coupons")}
					</Button>,
				]}
				afterOpenChange={(open) => {
					if (open) {
						errorMessageInputRef.current?.focus();
					}
				}}
			>
				{/* Error message input field */}
				<div className="tw-relative">
					<div className="tw-flex-1">
						{next && next.state === "OR" ? (
							<Input.TextArea
								autoSize
								required
								placeholder={__(
									"* Since there's an OR switch after this group, this error message will never be displayed. If all the conditions of this group fails, we will check the next group, and if that fails, its error message will be shown instead.",
									"swift-coupons"
								)}
								disabled={true}
								style={{
									minHeight: "64px",
									paddingRight: "auto",
								}}
							/>
						) : (
							<FloatInput.TextArea
								inputRef={errorMessageInputRef}
								label={__("Error Message", "swift-coupons")}
								autoSize
								autoFocus
								required
								placeholder={__(
									"Write your custom error message here...",
									"swift-coupons"
								)}
								defaultValue={
									data.settings["error_message"] || ""
								}
								disabled={confirmDelete}
								style={{
									minHeight: "64px",
								}}
								helptext={__(
									"Use this field to customize the error message that will be displayed to customers if their coupon fails to apply during checkout. This personalized message will replace the default error message, providing a more tailored and informative response to your customers, enhancing their shopping experience. Please ensure the message is clear and concise.",
									"swift-coupons"
								)}
							/>
						)}
					</div>
				</div>
			</Modal>
		</>
	);
};

// Props type definition for the RulePicker component
type RulePickerProps = {
	groupIndex: number; // Index of the group
	onChoose: (rule: string, groupIndex: number) => void; // Callback to choose a rule
	className?: string; // Optional class name for styling
};

const RulePicker = ({
	groupIndex,
	onChoose,
	className = "",
}: RulePickerProps) => {
	return (
		<BetterSelect
			showSearch // Enable search functionality
			className={`tw-w-full ${className}`}
			onChange={(value: string) => {
				onChoose(value, groupIndex); // Trigger onChoose callback
			}}
			value={null}
			placeholder={__("Add a rule...", "swift-coupons")}
			options={Object.keys(ruleCategories).map((catId: string) => ({
				label: ruleCategories[catId].title,
				options: [...ruleCategories[catId].rules]
					.sort((a: any, b: any) => {
						const lockedA = a?.locked || false;
						const lockedB = b?.locked || false;

						// Sort locked: false before locked: true
						if (lockedA !== lockedB) {
							return lockedA ? 1 : -1;
						}

						// Then sort by title alphabetically
						return a.title.localeCompare(b.title);
					})
					.map((rule: any) => ({
						value: rule.id,
						label: rule.title,
						description: rule.description || "No description",
						premiumLocked: rule.locked || false, // Indicate if the rule is locked
					})),
			}))}
		/>
	);
};

export default Group;
