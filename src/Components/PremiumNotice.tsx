import React from "react"; // Import React for JSX and cloneElement
import { SettingOutlined } from "@ant-design/icons";
import { __ } from "@wordpress/i18n";

type Props = {
	modal?: boolean;
	refer: string;
	icon?: React.ReactNode;
	title?: string;
	description?: string;
	buttonText?: string;
	className?: string;
};

const PremiumNotice = ({
	modal = false,
	refer = "",
	icon = (
		<SettingOutlined
			style={{ fontSize: modal ? 48 : 24, color: "#D97706" }}
		/>
	),
	title = __("This is a Premium Feature", "swift-coupons"),
	description = __(
		"Unlock this feature by upgrading to Swift Coupons Premium!",
		"swift-coupons"
	),
	buttonText = __("Get Premium", "swift-coupons"),
	className = "",
}: Props) => {
	// If modal is true, render the beautiful, highlighted modal design.
	if (modal) {
		return (
			<div
				className={`tw-relative tw-max-w-md tw-p-8 tw-bg-yellow-50 tw-flex tw-flex-col tw-items-center tw-gap-2 tw-text-center ${className}`}
			>
				{/* Enhanced Icon with a background */}
				<div className="tw-p-4 tw-bg-yellow-100 tw-rounded-full">
					{icon}
				</div>

				{/* Content Section */}
				<div className="tw-flex tw-flex-col tw-gap-2">
					<h2 className="tw-font-bold tw-text-yellow-900 tw-text-2xl">
						{title}
					</h2>
					<p className="tw-text-yellow-700 tw-text-base tw-leading-relaxed">
						{description}
					</p>
				</div>

				{/* Upgraded Call-to-Action Button */}
				<a
					href={`https://swiftcoupons.com/?utm_source=plugin&utm_medium=feature-lock&utm_campaign=${refer}`}
					target="_blank"
					rel="noopener noreferrer"
					className="tw-inline-block tw-mt-4 tw-px-8 tw-py-3 tw-bg-yellow-500 hover:tw-bg-yellow-600 tw-text-white hover:tw-text-whitetw-text-white tw-font-bold tw-text-base tw-rounded-lg tw-shadow-lg hover:tw-shadow-xl tw-transition-all tw-duration-300 tw-ease-in-out tw-no-underline tw-transform hover:tw-scale-105"
				>
					{buttonText}
				</a>
			</div>
		);
	}

	// Original design for the non-modal (inline) version.
	return (
		<div
			className={`tw-p-4 tw-bg-yellow-50 tw-border tw-border-yellow-300 tw-rounded-lg tw-flex tw-items-center tw-gap-3 ${className}`}
		>
			{icon}
			<div>
				<div className="tw-font-bold tw-text-yellow-800 tw-text-base">
					{title}
				</div>
				<div className="tw-text-yellow-700 tw-mt-1">{description}</div>
				<a
					href={`https://swiftcoupons.com/?utm_source=plugin&utm_medium=feature-lock&utm_campaign=${refer}`}
					target="_blank"
					rel="noopener noreferrer"
					className="tw-inline-block tw-mt-2 tw-px-4 tw-py-2 tw-bg-yellow-500 hover:tw-bg-yellow-600 tw-text-white hover:tw-text-white tw-font-semibold tw-rounded tw-shadow-sm tw-transition-colors tw-no-underline"
				>
					{buttonText}
				</a>
			</div>
		</div>
	);
};

export default PremiumNotice;
