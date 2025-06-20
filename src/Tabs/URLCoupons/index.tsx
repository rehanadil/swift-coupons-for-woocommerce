// Importing necessary dependencies and components
import "../../globals.d.ts";
import { useState, useEffect } from "@wordpress/element";
import { Card, Switch, Input, Button, message, Typography } from "antd";
import { CopyOutlined } from "@ant-design/icons";
import { __ } from "@wordpress/i18n"; // Import WordPress translation function

const { TextArea } = Input;
const { Title, Paragraph, Link } = Typography;

// Configuring the message component to display notifications at the top of the screen
message.config({
	top: 100,
});

const URLCoupons: React.FC = () => {
	// State to manage whether URL coupon functionality is enabled
	const [enabled, setEnabled] = useState(
		swiftCouponSingle.data?.url_apply?.enabled || false
	);
	// State to manage the custom code override for the coupon
	const [codeOverride, setCodeOverride] = useState(
		swiftCouponSingle.data?.url_apply?.code_override || ""
	);
	// State to manage the redirect URL after applying the coupon
	const [redirectToURL, setRedirectToURL] = useState(
		swiftCouponSingle.data?.url_apply?.redirect_to_url || ""
	);
	// State to manage whether to redirect back to the original page
	const [redirectBackToOrigin, setRedirectBackToOrigin] = useState(
		swiftCouponSingle.data?.url_apply?.redirect_back_to_origin || false
	);

	// Effect to dispatch custom events whenever the state changes
	useEffect(() => {
		const event = new CustomEvent("swiftcou-coupon-data-changed", {
			detail: {
				type: "url_apply",
				data: {
					enabled: enabled,
					code_override: codeOverride,
					redirect_to_url: redirectToURL,
					redirect_back_to_origin: redirectBackToOrigin,
				},
			},
		});
		window.dispatchEvent(event);

		const event2 = new CustomEvent("swiftcou-coupon-data-changed", {
			detail: {
				type: "url_apply_override_code",
				data: codeOverride,
			},
		});
		window.dispatchEvent(event2);
	}, [enabled, codeOverride, redirectToURL, redirectBackToOrigin]);

	// Function to handle copying the coupon URL to the clipboard
	const handleCopy = (url: string) => {
		navigator.clipboard.writeText(url);
		message.success(__("Coupon URL copied to clipboard!", "swift-coupons"));
	};

	return (
		<div className="tw-px-4 tw-pb-4 tw-flex tw-flex-col tw-gap-4">
			{/* Header section with title and description */}
			<div className="tw-flex tw-flex-col">
				<Title level={3} className="tw-mt-4">
					{__("URL Coupons", "swift-coupons")}
				</Title>
				<Paragraph>
					{__(
						"Allow your customers to apply this coupon by visiting a URL. This coupon will generate a unique coupon URL which can be used in all sorts of scenarios (e.g., email marketing, blog links, live chat support).",
						"swift-coupons"
					)}
				</Paragraph>
			</div>

			{/* Toggle to enable or disable URL coupon functionality */}
			<div className="tw-flex tw-flex-col tw-gap-1">
				<div className="tw-flex tw-items-center tw-gap-2">
					<Switch checked={enabled} onChange={setEnabled} />
					<span className="form-label tw-text-sm tw-font-semibold">
						{__("Enable Coupon URL", "swift-coupons")}
					</span>
				</div>

				<div className="tw-text-sm tw-text-gray-500">
					{__(
						"When checked, it enables the coupon URL functionality for the current coupon.",
						"swift-coupons"
					)}
				</div>
			</div>

			{/* Display the generated coupon URL */}
			<div className="tw-flex tw-flex-col tw-gap-1">
				<Input
					value={`${swiftCP.siteUrl}/coupon/${
						codeOverride || swiftCouponSingle.coupon?.code
					}`}
					readOnly
					addonAfter={
						<CopyOutlined
							onClick={() =>
								handleCopy(
									`${swiftCP.siteUrl}/coupon/${
										codeOverride ||
										swiftCouponSingle.coupon?.code
									}`
								)
							}
							className="tw-text-slate-500"
						/>
					}
					disabled={!enabled}
				/>
				<div className="tw-text-sm tw-text-gray-500">
					{__(
						"Visitors to this link will have the coupon code applied to their cart automatically.",
						"swift-coupons"
					)}
				</div>
			</div>

			{/* Input for overriding the coupon code in the URL */}
			<div className="tw-flex tw-flex-col tw-gap-1">
				<span className="form-label">
					{__("Code URL Override", "swift-coupons")}
				</span>
				<Input
					placeholder={__("Enter URL override", "swift-coupons")}
					disabled={!enabled}
					defaultValue={codeOverride}
					onChange={(e) => setCodeOverride(e.target.value)}
				/>
			</div>

			{/* Input for specifying the redirect URL */}
			<div className="tw-flex tw-flex-col tw-gap-1">
				<span className="form-label">
					{__("Redirect To URL", "swift-coupons")}
				</span>
				<Input
					defaultValue={redirectToURL || `${swiftCP.siteUrl}/cart`}
					disabled={!enabled}
					onChange={(e) => setRedirectToURL(e.target.value)}
				/>
			</div>

			{/* Toggle to enable or disable redirecting back to the origin page */}
			<div className="tw-flex tw-flex-col tw-gap-1">
				<div className="tw-flex tw-items-center tw-gap-2">
					<Switch
						disabled={!enabled}
						defaultValue={redirectBackToOrigin}
						onChange={setRedirectBackToOrigin}
					/>
					<span className="form-label">
						{__("Redirect back to origin", "swift-coupons")}
					</span>
				</div>
				<div className="tw-text-sm tw-text-gray-500">
					{__(
						"When checked, the user will be redirected back to the original page they were in after the coupon has been applied to the cart.",
						"swift-coupons"
					)}
				</div>
			</div>
		</div>
	);
};

export default URLCoupons;
