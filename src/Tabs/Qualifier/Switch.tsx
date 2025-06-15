// Importing the Ant Design Switch component
import { Switch as AntdSwitch } from "antd";

// Defining the Props type for the Switch component
// This includes the structure of the props passed to the component
type Props = {
	state?: "AND" | "OR"; // Current state of the switch, either "AND" or "OR"
	placement?: "outside_group" | "inside_group"; // Placement of the switch relative to a group
	onChange: (state: "AND" | "OR") => void; // Callback function triggered when the switch state changes
};

const Switch = ({ state, placement, onChange }: Props) => {
	return (
		// Wrapper div with conditional styling based on placement
		<div className={placement === "inside_group" ? "-tw-mx-3" : ""}>
			{/* Horizontal line with centered switch */}
			<div className="tw-flex tw-justify-center tw-items-center tw-relative tw-horizontal-line tw-w-full">
				{/* Container for the switch with background styling based on placement */}
				<div
					className={`tw-px-3 tw-z-10 ${
						placement === "inside_group"
							? "tw-bg-zinc-100" // Light gray background for inside group
							: "tw-bg-white" // White background for outside group
					}`}
				>
					{/* Ant Design Switch component */}
					<AntdSwitch
						checked={state === "OR"} // Determines if the switch is in the "OR" state
						onChange={(checked: boolean) => {
							// Trigger the onChange callback with the new state
							onChange(checked ? "OR" : "AND");
						}}
						checkedChildren="OR" // Label displayed when the switch is checked
						unCheckedChildren="AND" // Label displayed when the switch is unchecked
						style={{
							backgroundColor:
								state === "OR" ? "#eaa808" : "#08adea", // Dynamic background color
						}}
					/>
				</div>
			</div>
		</div>
	);
};

export default Switch;
