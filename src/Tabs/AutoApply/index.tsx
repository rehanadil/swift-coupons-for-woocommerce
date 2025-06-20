// Import React and useState for managing state
import { useState, useEffect } from "@wordpress/element";
// Import Ant Design components for UI
import { Switch } from "antd"; // Removed unused imports: Select, Form
// Import specific typography components from Ant Design
import Paragraph from "antd/es/typography/Paragraph";
import Title from "antd/es/typography/Title";
// Import WordPress translation function
import { __ } from "@wordpress/i18n";
import Notice from "../../Components/Notice";
import { RetweetOutlined, UnlockOutlined } from "@ant-design/icons";

// Declare a global variable for plugin-specific data
declare var swiftCouponSingle: {
	data: {
		auto_apply: {
			enabled: boolean; // Whether auto-apply is enabled
			allow_user_to_remove: boolean; // Whether users can remove the coupon
		};
	};
};

// Define the AutoApply component
const AutoApply: React.FC = () => {
	// State for whether auto-apply is enabled
	const [enabled, setEnabled] = useState(
		swiftCouponSingle.data?.auto_apply?.enabled || false
	);
	// State for whether users can remove the coupon
	const [allowUserToRemove, setAllowUserToRemove] = useState(
		swiftCouponSingle.data?.auto_apply?.allow_user_to_remove || false
	);

	// Effect to dispatch a custom event when state changes
	useEffect(() => {
		const event = new CustomEvent("swiftcou-coupon-data-changed", {
			detail: {
				type: "auto_apply", // Event type
				data: {
					enabled: enabled, // Current enabled state
					allow_user_to_remove: allowUserToRemove, // Current allowUserToRemove state
				},
			},
		});
		window.dispatchEvent(event); // Dispatch the event
	}, [enabled, allowUserToRemove]); // Dependencies for the effect

	return (
		// Main container with padding and flex layout
		<div className="tw-px-4 tw-flex tw-flex-col tw-gap-4">
			{/* Section for title and description */}
			<div className="tw-flex tw-flex-col">
				<Title level={3} className="tw-mt-4">
					{/* Title for the feature */}
					{__("Auto Apply Coupon", "swift-coupons")}
				</Title>
				<Paragraph>
					{/* Description of the feature */}
					{__(
						"The Auto Apply Coupon feature allows you to automatically apply a coupon to a customer's cart when specific conditions are met. This eliminates the need for customers to manually enter coupon codes, providing a seamless shopping experience. Additionally, you can configure whether users are allowed to remove the automatically applied coupon from their cart.",
						"swift-coupons"
					)}
				</Paragraph>
			</div>

			<Notice.Premium
				unlocked={swiftCP.isPremium}
				refer="auto-apply"
				icon={
					swiftCP.isPremium ? (
						<UnlockOutlined
							style={{ fontSize: 24, color: "#06d9d9" }}
						/>
					) : (
						<RetweetOutlined
							style={{ fontSize: 24, color: "#D97706" }}
						/>
					)
				}
				title={__("Auto Apply is a Premium Feature", "swift-coupons")}
				description={__(
					swiftCP.isPremium
						? "Congratulations! You now have access to this feature. Enjoy!"
						: "Unlock auto apply by upgrading to Swift Coupons Premium.",
					"swift-coupons"
				)}
				className="-tw-mt-4"
			/>

			{/* Section for switches */}
			<div
				className={`tw-flex tw-flex-col tw-gap-6 ${
					swiftCP.isPremium
						? ""
						: "tw-opacity-70 tw-pointer-events-none"
				}`}
			>
				{/* Switch for enabling auto-apply */}
				<div className="tw-flex tw-gap-2 tw-items-center">
					<Switch checked={enabled} onChange={setEnabled} />
					<span className="tw-text-sm tw-font-semibold">
						{__("Enable Auto Apply Coupon", "swift-coupons")}
					</span>
				</div>

				{/* Section for allowing users to remove the coupon */}
				<div className="tw-flex tw-flex-col tw-gap-2">
					<div className="tw-flex tw-gap-2 tw-items-center">
						<Switch
							checked={allowUserToRemove}
							onChange={setAllowUserToRemove}
							disabled={!enabled} // Disable if auto-apply is not enabled
						/>
						<span className="tw-text-sm tw-font-semibold">
							{__(
								"Allow User to remove this coupon",
								"swift-coupons"
							)}
						</span>
					</div>

					{/* Description for the allow user to remove option */}
					<div className="tw-text-sm tw-text-gray-400">
						{__(
							"This option allows users to remove the auto-applied coupon from their cart.",
							"swift-coupons"
						)}
					</div>
				</div>
			</div>
		</div>
	);
};

// Export the AutoApply component as default
export default AutoApply;
