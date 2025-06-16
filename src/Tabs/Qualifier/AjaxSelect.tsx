// Importing necessary libraries and components
import React, { useMemo, useRef, useState } from "react";
import { Select, Spin } from "antd";
import type { SelectProps } from "antd";
import debounce from "lodash/debounce";
import { __ } from "@wordpress/i18n"; // Import WordPress translation function

// Define the UserValue type for use in the component
interface UserValue {
	label: string; // Label for the option
	value: string; // Value for the option
}

// Interface for DebounceSelectProps
// Extends Ant Design's SelectProps and adds custom properties
export interface DebounceSelectProps<ValueType = any>
	extends Omit<SelectProps<ValueType | ValueType[]>, "options" | "children"> {
	fetchOptions: (url: string, search: string) => Promise<ValueType[]>; // Function to fetch options
	debounceTimeout?: number; // Debounce timeout for search
	url: string; // API endpoint URL
}

// DebounceSelect component
// A Select component with debounced search functionality
function DebounceSelect<
	ValueType extends {
		label: React.ReactNode; // Label for the option
		value: string | number; // Value for the option
	} = any,
>({
	fetchOptions,
	debounceTimeout = 800, // Default debounce timeout
	url,
	...props
}: DebounceSelectProps<ValueType>) {
	const [fetching, setFetching] = useState(false); // State to track fetching status
	const [options, setOptions] = useState<ValueType[]>([]); // State to store fetched options
	const fetchRef = useRef(0); // Reference to track fetch requests

	// Memoized function to debounce the fetchOptions call
	const debounceFetcher = useMemo(() => {
		const loadOptions = (url: string, value: string) => {
			fetchRef.current += 1; // Increment fetch reference
			const fetchId = fetchRef.current; // Store current fetch ID
			setOptions([]); // Clear options before fetching
			setFetching(true); // Set fetching state to true

			fetchOptions(url, value).then((newOptions) => {
				if (fetchId !== fetchRef.current) {
					// Ignore outdated fetch results
					return;
				}

				setOptions(newOptions); // Update options with fetched data
				setFetching(false); // Set fetching state to false
			});
		};

		return debounce(loadOptions, debounceTimeout); // Return debounced function
	}, [fetchOptions, debounceTimeout]);

	return (
		<Select
			showSearch // Enable search functionality
			labelInValue // Return label and value as an object
			filterOption={false} // Disable default filtering
			onSearch={(value) => debounceFetcher(url, value)} // Call debounced fetcher on search
			notFoundContent={fetching ? <Spin size="small" /> : null} // Show spinner while fetching
			{...props} // Spread additional props
			options={options} // Set options for the Select component
		/>
	);
}

// Function to fetch user list from the API
// Replaces {query} in the URL with the search query
async function fetchUserList(url: string, query: string): Promise<UserValue[]> {
	const updatedURL = url.includes("{query}")
		? url.replace("{query}", encodeURIComponent(query))
		: url +
		  (url.includes("?") ? "&" : "?") +
		  "query=" +
		  encodeURIComponent(query);

	return fetch(updatedURL)
		.then((response) => response.json()) // Parse JSON response
		.then((body) =>
			body.results.map((result: { value: string; label: string }) => ({
				value: result.value, // Map value
				label: result.label, // Map label
			}))
		);
}

// AjaxSelect component
// Wrapper around DebounceSelect to handle single and multiple selection modes
const AjaxSelect = ({
	url,
	defaultValue = null, // Default value for the Select component
	placeholder,
	mode = undefined, // Selection mode (single or multiple)
	onChange, // Callback for value change
}: {
	url: string;
	defaultValue?: any;
	placeholder?: string;
	mode?: SelectProps["mode"];
	onChange: (value: any) => void;
}) => {
	if (mode === "multiple") {
		// Handle multiple selection mode
		const [value, setValue] = useState<UserValue[]>(defaultValue);

		return (
			<DebounceSelect
				mode="multiple"
				value={value} // Current value
				defaultValue={defaultValue} // Default value
				placeholder={
					placeholder ?? __("Type to search...", "swift-coupons")
				} // Placeholder text
				fetchOptions={fetchUserList} // Fetch options function
				url={url} // API endpoint URL
				onChange={(newValue) => {
					setValue(newValue as UserValue[]); // Update state with new value
					onChange(newValue); // Trigger onChange callback
				}}
				style={{ width: "100%" }} // Set width to 100%
			/>
		);
	} else {
		// Handle single selection mode
		const [value, setValue] = useState<UserValue>(defaultValue);

		return (
			<DebounceSelect
				value={value} // Current value
				defaultValue={defaultValue} // Default value
				placeholder={
					placeholder ?? __("Type to search...", "swift-coupons")
				} // Placeholder text
				fetchOptions={fetchUserList} // Fetch options function
				url={url} // API endpoint URL
				onChange={(newValue) => {
					setValue(newValue as UserValue); // Update state with new value
					onChange(newValue); // Trigger onChange callback
				}}
				style={{ width: "100%" }} // Set width to 100%
			/>
		);
	}
};

export default AjaxSelect;
