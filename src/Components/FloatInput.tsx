// Importing necessary libraries and components
import { useState } from "@wordpress/element";
import { Input, Tooltip } from "antd";
import {
	InputProps as BaseInputProps,
	TextAreaProps as BaseTextAreaProps,
	SearchProps as BaseSearchProps,
} from "antd/es/input";
import { QuestionCircleOutlined } from "@ant-design/icons";

// Defining additional properties for labels
// These properties extend the base input properties from Ant Design
type LabelProps = {
	label: string; // Label text for the input
	helptext?: string; // Optional help text displayed as a tooltip
	inputRef?: any; // Reference to the input element
};

// Extending Ant Design input types with label properties
type InputProps = BaseInputProps & LabelProps;
type TextAreaProps = BaseTextAreaProps & LabelProps;
type SearchProps = BaseSearchProps & LabelProps;

// Component to render a floating label
const FloatLabel = ({ text, float }: { text: string; float: boolean }) => {
	return (
		<label
			className={`tw-absolute tw-pointer-events-none tw-font-normal tw-text-gray-400 tw-transition-all tw-duration-200 tw-ease-linear ${
				float
					? "tw-left-2 -tw-top-2 tw-text-[10px] tw-bg-white tw-py-0 tw-px-1 tw-rounded-t-[4px] "
					: "tw-left-3 tw-top-[6px]"
			}`}
		>
			{text}
			{/* Uncomment the following line if required fields need an asterisk */}
			{/* props.required ? <span className="text-danger">*</span> : null */}
		</label>
	);
};

// Component for a floating input field
const FloatInput = ({ label, helptext, inputRef, ...props }: InputProps) => {
	// State to track focus on the input field
	const [focus, setFocus] = useState(false);

	// Determine if the label should float
	const float = focus || props.value || props.defaultValue ? true : false;

	// Adjust input properties based on floating state
	const inputProps = {
		...props,
		placeholder: float ? props.placeholder : "",
	};

	return (
		<div
			className="tw-relative"
			onBlur={() => setFocus(false)} // Handle blur event
			onFocus={() => setFocus(true)} // Handle focus event
		>
			<Input
				ref={inputRef} // Reference to the input element
				className={helptext ? "tw-pr-8" : ""} // Adjust padding if helptext exists
				{...inputProps}
			/>
			<FloatLabel text={label} float={float} />
			{helptext && (
				<div className="tw-absolute tw-top-1 tw-right-2 tw-cursor-pointer">
					<Tooltip placement="left" arrow title={helptext}>
						<QuestionCircleOutlined />
					</Tooltip>
				</div>
			)}
		</div>
	);
};

// Component for a floating text area
const TextArea = ({ label, helptext, inputRef, ...props }: TextAreaProps) => {
	// State to track focus on the text area
	const [focus, setFocus] = useState(false);

	// Determine if the label should float
	const float = focus || props.value || props.defaultValue ? true : false;

	// Adjust text area properties based on floating state
	const inputProps = {
		...props,
		placeholder: float ? props.placeholder : "",
	};

	return (
		<div
			className="tw-relative"
			onBlur={() => setFocus(false)} // Handle blur event
			onFocus={() => setFocus(true)} // Handle focus event
		>
			<Input.TextArea
				ref={inputRef} // Reference to the text area element
				className={helptext ? "tw-pr-8" : ""} // Adjust padding if helptext exists
				{...inputProps}
			/>
			<FloatLabel text={label} float={float} />
			{helptext && (
				<div className="tw-absolute tw-top-1 tw-right-2 tw-cursor-pointer">
					<Tooltip placement="left" arrow title={helptext}>
						<QuestionCircleOutlined />
					</Tooltip>
				</div>
			)}
		</div>
	);
};

// Component for a floating search input
const Search = ({ label, helptext, inputRef, ...props }: SearchProps) => {
	// State to track focus on the search input
	const [focus, setFocus] = useState(false);

	// Determine if the label should float
	const float = focus || props.value || props.defaultValue ? true : false;

	// Adjust search input properties based on floating state
	const inputProps = {
		...props,
		placeholder: float ? props.placeholder : "",
	};

	return (
		<div
			className="tw-relative"
			onBlur={() => setFocus(false)} // Handle blur event
			onFocus={() => setFocus(true)} // Handle focus event
		>
			<Input.Search
				ref={inputRef} // Reference to the search input element
				className={helptext ? "tw-pr-8" : ""} // Adjust padding if helptext exists
				{...inputProps}
			/>
			<FloatLabel text={label} float={float} />
			{helptext && (
				<div className="tw-absolute tw-top-1 tw-right-2 tw-cursor-pointer">
					<Tooltip placement="left" arrow title={helptext}>
						<QuestionCircleOutlined />
					</Tooltip>
				</div>
			)}
		</div>
	);
};

// Attach TextArea and Search components to FloatInput
FloatInput.TextArea = TextArea;
FloatInput.Search = Search;

export default FloatInput;
