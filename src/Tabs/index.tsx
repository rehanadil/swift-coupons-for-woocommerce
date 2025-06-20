// Import necessary modules from React and other libraries
import { Suspense, lazy } from "@wordpress/element";
import { createRoot } from "react-dom/client";
import { StyleProvider } from "@ant-design/cssinjs";
import { ConfigProvider } from "antd";

// Lazy load components for better performance
const Qualifier = lazy(() => import("./Qualifier"));
const BXGX = lazy(() => import("./BXGX"));
const Scheduler = lazy(() => import("./Scheduler"));
const URLCoupons = lazy(() => import("./URLCoupons"));
const AutoApply = lazy(() => import("./AutoApply"));

// Declare a global variable for plugin-specific data
declare var swiftCouponSingle: {
	pluginUrl: string; // URL of the plugin
};

// Extend the Window interface to include the _bal property
declare global {
	interface Window {
		_bal?: any;
	}
}

// Define a mapping of DOM selectors to React components
const Tabs: { [key: string]: React.FC } = {
	swiftcou_qualifiers_root: Qualifier, // Qualifier component
	swiftcou_bxgx_deals_root: BXGX, // BXGX component
	swiftcou_scheduler_root: Scheduler, // Scheduler component
	swiftcou_url_apply_root: URLCoupons, // URLCoupons component
	swiftcou_auto_apply_root: AutoApply, // AutoApply component
};

// Iterate over each selector in the Tabs object
for (const selector in Tabs) {
	const Relement = Tabs[selector]; // Get the React component
	const container = document.getElementById(selector)!; // Get the DOM element by ID

	if (!container) {
		console.warn(`Container with ID ${selector} not found.`);
		continue; // Skip if the container does not exist
	}

	const shadowContainer = container.attachShadow({ mode: "open" }); // Create a shadow DOM

	// Add a stylesheet link to the shadow DOM
	const link = document.createElement("link");
	link.rel = "stylesheet";
	link.href = `${swiftCouponSingle.pluginUrl}/assets/css/style.css`;
	shadowContainer.appendChild(link);

	// Create a div element to serve as the root for the React component
	const shadowRootElement = document.createElement("div");
	shadowContainer.appendChild(shadowRootElement);

	// Use Suspense with a fallback when loading components lazily
	createRoot(shadowRootElement).render(
		<StyleProvider container={shadowContainer}>
			<ConfigProvider
				getPopupContainer={() => shadowRootElement} // Set popup container to shadow root
				theme={{
					token: {
						zIndexPopupBase: 1001, // Set base z-index for popups
						colorPrimary: "#8b5cf6", // Set primary color
					},
				}}
			>
				<Suspense fallback={<div>Loading...</div>}>
					<Relement />
				</Suspense>
			</ConfigProvider>
		</StyleProvider>
	);
}

// Add an event listener for custom events
window.addEventListener("swiftcou-coupon-data-changed", (e: any) => {
	const { type, data } = e.detail; // Extract event details

	const form = document.querySelector("form#post") as HTMLFormElement; // Get the form element
	const input = document.querySelector(
		`input[name="_swiftcou_${type}"]`
	) as HTMLInputElement; // Get the input element by name

	// If data is an object, stringify it
	const value = typeof data === "object" ? JSON.stringify(data) : data;

	window._bal = data;

	if (!input) {
		// If input does not exist, create a new hidden input
		const newInput = document.createElement("input");
		newInput.type = "hidden";
		newInput.name = `_swiftcou_${type}`;
		newInput.value = value;
		form.appendChild(newInput); // Append the new input to the form
	} else {
		input.value = value; // Update the value of the existing input
		console.error("input value updated", input.value, "...", value);
	}
});
