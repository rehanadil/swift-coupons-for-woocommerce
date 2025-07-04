import { Select } from "antd";
import { __ } from "@wordpress/i18n";
import type { SelectProps } from "antd";
import type { Element } from "@wordpress/element";

// --- TYPE DEFINITIONS ---
// The structure for each option in our custom select component.
export interface BetterSelectOption {
	value: string;
	label: string;
	icon?: Element;
	description?: string;
	className?: string; // Optional class for custom styling
	tags?: (Element | false)[];
	dimmed?: boolean; // Optional flag to apply opacity styling
}

// Support for option groups
export interface BetterSelectGroup {
	label: string;
	options: BetterSelectOption[];
	key?: string | number;
	className?: string;
}

type BetterSelectItem = BetterSelectOption | BetterSelectGroup;

// The props for our main component, extending Ant Design's SelectProps.
export interface BetterSelectProps
	extends Omit<SelectProps<string>, "options" | "optionRender"> {
	options: BetterSelectItem[];
	descriptionIndent?: boolean; // Optional indent for description text
	onChange: any;
}

// --- THE ENHANCED SELECT COMPONENT ---
const BetterSelect: React.FC<BetterSelectProps> = ({
	options,
	className,
	descriptionIndent = true, // Default to indent for descriptions
	...rest
}) => {
	return (
		<Select
			{...rest}
			// --- STYLING & CLASSNAMES ---
			// Apply a base class for the select input itself and allow overriding.
			className={className || ""}
			// Apply custom classes to the dropdown panel for a modern card-like appearance.
			popupClassName="tw-better-select-popup"
			// --- DATA & RENDERING ---
			// Pass the options array directly to Ant Design.
			// We include the full option object so we can access icon/description in optionRender.
			options={options}
			// `optionRender` is the correct way to customize the appearance of each item in the dropdown.
			// This preserves all of AntD's built-in functionality (keyboard navigation, selection).
			optionRender={(option) => {
				// Handle OptGroup rendering
				if (option?.group) {
					const group = option.data as BetterSelectGroup;
					return (
						<div
							className={`tw-better-select-group-label ${
								group.className || ""
							} tw-font-semibold tw-text-gray-600 tw-px-2 tw-py-1 tw-bg-gray-50 tw-uppercase tw-text-xs tw-tracking-wide`}
						>
							{group.label}
						</div>
					);
				}
				// The `option.data` object contains the full BetterSelectOption record.
				const {
					label,
					icon,
					description,
					className,
					tags = [],
					dimmed = false,
				} = option.data as BetterSelectOption;
				return (
					// The main container for each option item.
					// We use a flexbox to align the icon and the text content.
					<div
						className={`tw-flex tw-flex-col tw-items-start tw-w-full tw-gap-1 ${className}`}
					>
						<div className="tw-flex tw-items-center tw-w-full tw-gap-2">
							{/* Icon is aligned to the start of the flex container */}
							{icon && (
								<span
									className={`tw-text-gray-400 tw-flex-shrink-0 tw-mt-0.5 ${
										dimmed ? "tw-opacity-60" : ""
									}`}
								>
									{icon}
								</span>
							)}

							{/* A new flex column stacks the label and description vertically. */}
							<div className="tw-flex tw-flex-col">
								<div className="tw-flex tw-gap-2">
									<span
										className={`tw-font-medium tw-text-gray-800 ${
											dimmed ? "tw-opacity-60" : ""
										}`}
									>
										{label}
									</span>
									{/* If tags are provided, render them as inline elements */}
									<div className="tw-flex tw-gap-1.5">
										{tags && tags.map((tag, index) => tag)}
									</div>
								</div>
								{description && descriptionIndent && (
									<p
										className={`tw-text-xs tw-text-gray-500 tw-mt-0.5 tw-whitespace-normal ${
											dimmed ? "tw-opacity-60" : ""
										}`}
									>
										{description}
									</p>
								)}
							</div>
						</div>

						{description && !descriptionIndent && (
							<p
								className={`tw-text-xs tw-text-gray-500 tw-mt-0.5 tw-whitespace-normal ${
									dimmed ? "tw-opacity-60" : ""
								}`}
							>
								{description}
							</p>
						)}
					</div>
				);
			}}
		/>
	);
};

export default BetterSelect;
