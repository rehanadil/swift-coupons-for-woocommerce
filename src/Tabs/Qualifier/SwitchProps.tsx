// Defining the SwitchProps type
// This type represents the structure of a switch object used in the application
type SwitchProps = {
	// Specifies the type of the object, which is always "switch"
	type: "switch";
	// Current state of the switch, either "AND" or "OR"
	state: "AND" | "OR";
};

export default SwitchProps;
