import { useState, useEffect } from "@wordpress/element"; // Import React for JSX and cloneElement
import { LockOutlined } from "@ant-design/icons";
import { __ } from "@wordpress/i18n";
import apiFetch from "@wordpress/api-fetch";
import { Button, Input, Form, Alert } from "antd";
import { StarIcon } from "lucide-react";
import type { Element } from "@wordpress/element";

type Props = {
	modal?: boolean;
	refer: string;
	icon?: Element;
	title?: string;
	description?: string;
	buttonText?: string;
	className?: string;
	unlocked?: boolean; // Optional prop to indicate if the feature is unlocked
};

const Premium = ({
	modal = false,
	refer = "",
	icon = (
		<LockOutlined style={{ fontSize: modal ? 48 : 24, color: "#D97706" }} />
	),
	title = __("This is a Premium Feature", "swift-coupons-for-woocommerce"),
	description = __(
		"Unlock this feature by upgrading to Swift Coupons Premium!",
		"swift-coupons-for-woocommerce"
	),
	buttonText = __("Get Premium", "swift-coupons-for-woocommerce"),
	className = "",
	unlocked = false,
}: Props) => {
	// If modal is true, render the beautiful, highlighted modal design.
	if (modal) {
		return (
			<div
				className={`tw-relative tw-max-w-md tw-p-8 tw-bg-gradient-to-b tw-from-yellow-50 tw-to-orange-100 tw-flex tw-flex-col tw-items-center tw-gap-2 tw-text-center ${className}`}
			>
				{/* Enhanced Icon with a background */}
				<div className="tw-p-4 tw-bg-yellow-100/75 tw-rounded-full">
					<LockOutlined
						style={{ fontSize: modal ? 48 : 24, color: "#D97706" }}
					/>
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
					className="tw-inline-block tw-mt-4 tw-px-8 tw-py-3 tw-bg-yellow-500 hover:tw-bg-yellow-600 tw-text-white hover:tw-text-white tw-font-bold tw-text-base tw-rounded-lg tw-shadow-lg hover:tw-shadow-xl tw-transition-all tw-duration-300 tw-ease-in-out tw-no-underline tw-transform hover:tw-scale-105"
				>
					{buttonText}
				</a>
			</div>
		);
	}

	// Original design for the non-modal (inline) version.
	return (
		<div
			className={`tw-p-4 tw-border tw-rounded-lg tw-flex tw-items-center tw-gap-3 ${
				unlocked
					? "tw-bg-teal-50 tw-border-teal-300"
					: "tw-bg-yellow-50 tw-border-yellow-300"
			} ${className}`}
		>
			{icon}
			<div>
				<div
					className={`tw-font-bold tw-text-base ${
						unlocked ? "tw-text-teal-800" : "tw-text-yellow-800"
					}`}
				>
					{title}
				</div>
				<div
					className={`tw-mt-1 ${
						unlocked ? "tw-text-teal-700" : "tw-text-yellow-700"
					}`}
				>
					{description}
				</div>
			</div>
		</div>
	);
};

export default {
	Premium,
};
