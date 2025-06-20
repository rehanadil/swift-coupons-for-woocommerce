// Importing necessary dependencies and components
import rules from "./rules";
import { useState, useEffect, useRef } from "@wordpress/element";
import { Col, Row, Input, Select, Tooltip, Modal, Button } from "antd";
import { QuestionCircleOutlined, SettingOutlined } from "@ant-design/icons";
import FloatInput from "../../Components/FloatInput";
import AjaxSelect from "./AjaxSelect";
import { __ } from "@wordpress/i18n";

// Defining the Props type for the Rule component
// This includes the structure of the props passed to the component
type Props = {
	groupIndex: number; // Index of the group this rule belongs to
	index: number; // Index of the rule within the group
	data: any; // Data associated with the rule
	onChange: (key: string | number, value: any) => void; // Callback for handling changes
	onRemove: (groupIndex: number, ruleIndex: number) => void; // Callback for removing the rule
	className?: string; // Optional class name for styling
	next: any; // Data for the next rule in the sequence
};

const Rule = ({
	groupIndex,
	index,
	data,
	onChange,
	onRemove,
	className,
	next,
}: Props) => {
	// State to manage the visibility of the settings dialog
	const [showDialog, setShowDialog] = useState<boolean>(false);
	// State to manage the confirmation for deletion
	const [confirmDelete, setConfirmDelete] = useState<boolean>(false);

	// Reference to the error message input field
	const errorMessageInputRef = useRef<any>(null);

	// Effect to reset the confirmDelete state when the dialog visibility changes
	useEffect(() => {
		setConfirmDelete(false);
	}, [showDialog]);

	// Function to handle closing the settings dialog
	const handleDialogClose = () => {
		setShowDialog(false);
	};

	// Fetching the rule definition based on the data ID
	const rule = rules[data.id];

	// If the rule is not found, return null to render nothing
	if (!rule) return null;

	// Extracting the inputs for the rule
	const ruleInputs = rule.inputs || [];

	return (
		<>
			{/* Main container for the rule */}
			<div className="tw-group/rule tw-flex tw-flex-col tw-gap-2">
				{/* Header section displaying the rule title and description */}
				<div className="tw-flex tw-items-center tw-gap-2">
					<span className="tw-text-slate-600 tw-text-base">
						{rule.title}
					</span>

					{/* Tooltip for displaying the rule description */}
					<Tooltip
						placement="bottom"
						arrow
						title={__(rule.description, "swift-coupons")}
						className="tw-cursor-pointer"
					>
						<QuestionCircleOutlined />
					</Tooltip>

					{/* Icon to open the settings dialog */}
					<div
						className="tw-p-0 tw-text-red-400 hover:tw-text-red-500 tw-opacity-0 group-hover/rule:tw-opacity-100 tw-transition-opacity tw-duration-200 tw-cursor-pointer"
						onClick={() => setShowDialog(true)}
					>
						<SettingOutlined className="tw-text-base" />
					</div>
				</div>

				{/* Rendering inputs for the rule dynamically based on its configuration */}
				<Row gutter={6}>
					{ruleInputs.map((input, inputIndex) => {
						switch (input.type) {
							case "text":
								return (
									<Col
										key={
											groupIndex +
											data.id +
											inputIndex +
											input.name
										}
										span={input.size * 24}
									>
										<FloatInput
											label={input.label || ""}
											placeholder={input.placeholder}
											className="tw-w-full tw-bg-white"
											defaultValue={
												data.data[input.name] ??
												input.value
											}
											onChange={(e) =>
												onChange(
													input.name,
													e.target.value
												)
											}
										/>
									</Col>
								);

							case "number":
								return (
									<Col
										key={
											groupIndex +
											data.id +
											inputIndex +
											input.name
										}
										span={input.size * 24}
									>
										<FloatInput
											label={input.label || ""}
											placeholder={input.placeholder}
											type="number"
											className="tw-w-full tw-bg-white"
											defaultValue={
												data.data[input.name] ??
												input.value
											}
											onChange={(e) =>
												onChange(
													input.name,
													e.target.value
												)
											}
										/>
									</Col>
								);

							case "select":
								if (input.search) {
									const arrayValues =
										input.options?.filter((o: any) => {
											return (
												o.value ===
												(data.data[input.name] ??
													input.value)
											);
										}) || [];
									const arrayValue =
										arrayValues.length > 0
											? arrayValues[0]
											: null;

									return (
										<Col
											key={
												groupIndex +
												data.id +
												inputIndex +
												input.name
											}
											span={input.size * 24}
										>
											<Select
												showSearch
												className="tw-w-full tw-bg-white"
												options={input.options || []}
												defaultValue={arrayValue}
												onChange={(value) =>
													onChange(input.name, value)
												}
												mode={
													input.multiple
														? input.tags
															? "tags"
															: "multiple"
														: undefined
												}
											/>
										</Col>
									);
								} else {
									return (
										<Col
											key={
												groupIndex +
												data.id +
												inputIndex +
												input.name
											}
											span={input.size * 24}
										>
											<Select
												className="tw-w-full tw-bg-white"
												defaultValue={
													data.data[input.name] ??
													input.value
												}
												onChange={(value) =>
													onChange(input.name, value)
												}
												mode={
													input.multiple
														? input.tags
															? "tags"
															: "multiple"
														: undefined
												}
												options={input.options || []}
											/>
										</Col>
									);
								}

							case "ajax-select":
								return (
									<Col
										key={
											groupIndex +
											data.id +
											inputIndex +
											input.name
										}
										span={input.size * 24}
									>
										<AjaxSelect
											url={input.url}
											defaultValue={
												data.data[input.name] ??
												input.value
											}
											placeholder={input.placeholder}
											onChange={(value: any) =>
												onChange(input.name, value)
											}
											mode={
												input.multiple
													? input.tags
														? "tags"
														: "multiple"
													: undefined
											}
										/>
									</Col>
								);
						}
					})}
				</Row>
			</div>

			{/* Modal for rule settings */}
			<Modal
				title={__("Rule Settings", "swift-coupons")}
				centered
				open={showDialog}
				onOk={() => handleDialogClose()}
				onCancel={() => handleDialogClose()}
				footer={[
					// Delete button with confirmation logic
					<Button
						key={groupIndex + data.id + index + "delete"}
						danger={!confirmDelete}
						type={confirmDelete ? "primary" : "default"}
						onClick={() => {
							if (confirmDelete) {
								onRemove(groupIndex, index);
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

					// Cancel button with conditional behavior
					<Button
						key={groupIndex + data.id + index + "cancel"}
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

					// Save button to update settings
					<Button
						key={groupIndex + data.id + index + "submit"}
						type="primary"
						className="tw-mx-1"
						disabled={confirmDelete}
						onClick={() => {
							onChange("settings", {
								error_message:
									errorMessageInputRef.current
										?.resizableTextArea?.textArea?.value ||
									"",
							});
							handleDialogClose();
						}}
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
				{/* Conditional rendering for error message input */}
				<div className="tw-relative">
					<div className="tw-flex-1">
						{next && next.state === "OR" ? (
							<Input.TextArea
								autoSize
								required
								placeholder={__(
									"* Since there's an OR switch after this condition, this error message will never be displayed. If this condition fails, we will check the next one, and if that fails, its error message will be shown instead.",
									"swift-coupons"
								)}
								disabled={true}
								style={{
									minHeight: "64px",
								}}
							/>
						) : (
							<FloatInput.TextArea
								inputRef={errorMessageInputRef}
								label={__("Error Message", "swift-coupons")}
								autoFocus
								autoSize
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

export default Rule;
