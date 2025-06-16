import React, { useState } from "react"; // Import React for JSX and cloneElement
import { SettingOutlined, StarFilled } from "@ant-design/icons";
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

const Premium = ({
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

const RatingUnlock = ({ pluginUrl = "#" }) => {
	const [email, setEmail] = useState("");
	const [submitted, setSubmitted] = useState(false);
	const [error, setError] = useState("");

	// --- Handlers ---
	const handleEmailChange = (e: React.ChangeEvent<HTMLInputElement>) => {
		setEmail(e.target.value);
		if (error) {
			setError(""); // Clear error when user starts typing
		}
	};

	const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
		e.preventDefault(); // Prevent form from reloading the page

		// Basic email validation
		if (!email || !/\S+@\S+\.\S+/.test(email)) {
			setError("Please enter a valid email address.");
			return;
		}

		console.log("Submitted email:", email);
		// In a real application, you would send the email to your server here.
		// For example:
		// fetch('/api/request-activation', {
		//     method: 'POST',
		//     headers: { 'Content-Type': 'application/json' },
		//     body: JSON.stringify({ email }),
		// });

		setSubmitted(true);
		setError("");
	};

	// --- Render Logic ---
	return (
		<div className="tw-relative tw-bg-white tw-w-full tw-max-w-lg tw-mx-auto tw-rounded-2xl tw-shadow-2xl tw-transform tw-transition-all tw-duration-300 tw-ease-in-out tw-scale-100">
			{/* Main Content Area */}
			<div className="tw-p-8 md:tw-p-10 tw-text-center">
				{/* Icon */}
				<div className="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-bg-blue-100 tw-rounded-full tw-mb-5">
					<StarFilled className="tw-w-9 tw-h-9 tw-text-blue-500" />
				</div>

				{submitted ? (
					// --- Thank You State ---
					<div className="tw-flex tw-flex-col tw-items-center">
						<h2 className="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-2">
							Thank You!
						</h2>
						<p className="tw-text-gray-600">
							We've received your request. Please check your inbox
							for the activation code. It should arrive shortly.
						</p>
					</div>
				) : (
					// --- Initial State ---
					<>
						{/* Headline */}
						<h2 className="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-3">
							Unlock this feature for FREE!
						</h2>

						{/* Main Call to Action */}
						<p className="tw-text-gray-600 tw-text-lg tw-mb-6 tw-leading-relaxed">
							Simply leave us a review on{" "}
							<a
								href={pluginUrl}
								target="_blank"
								rel="noopener noreferrer"
								className="tw-font-bold tw-text-blue-600 hover:tw-text-blue-700 tw-underline tw-transition-colors tw-duration-200"
							>
								WordPress.org
							</a>
							.
						</p>

						{/* Divider with text */}
						<div className="tw-flex tw-items-center tw-my-6">
							<hr className="tw-flex-grow tw-border-t tw-border-gray-200" />
							<span className="tw-mx-4 tw-text-sm tw-font-medium tw-text-gray-400">
								THEN
							</span>
							<hr className="tw-flex-grow tw-border-t tw-border-gray-200" />
						</div>

						{/* Form Section */}
						<p className="tw-text-gray-600 tw-mb-4">
							After leaving your review, enter your email below
							and we'll send your activation code.
						</p>

						<form onSubmit={handleSubmit} noValidate>
							<div className="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3">
								<input
									type="email"
									value={email}
									onChange={handleEmailChange}
									placeholder="you@example.com"
									aria-label="Email Address"
									className="tw-flex-grow tw-w-full tw-px-4 tw-py-3 tw-text-gray-800 tw-bg-gray-100 tw-border-2 tw-border-gray-200 tw-rounded-lg focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-border-transparent tw-transition-all"
								/>
								<button
									type="submit"
									className="tw-w-full sm:tw-w-auto tw-px-8 tw-py-3 tw-font-bold tw-text-white tw-bg-blue-600 tw-rounded-lg hover:tw-bg-blue-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-offset-2 focus:tw-ring-blue-500 tw-shadow-md hover:tw-shadow-lg tw-transform hover:tw--translate-y-0.5 tw-transition-all tw-duration-300"
								>
									Submit
								</button>
							</div>
							{error && (
								<p className="tw-text-red-500 tw-text-sm tw-mt-2 tw-text-left">
									{error}
								</p>
							)}
						</form>
					</>
				)}
			</div>
		</div>
	);
};

export default {
	Premium,
	RatingUnlock,
};
