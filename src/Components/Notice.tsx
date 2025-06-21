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
	title = __("This is a Premium Feature", "swift-coupons"),
	description = __(
		"Unlock this feature by upgrading to Swift Coupons Premium!",
		"swift-coupons"
	),
	buttonText = __("Get Premium", "swift-coupons"),
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

const RatingUnlock = ({ pluginUrl = "#" }) => {
	const [submitted, setSubmitted] = useState(false);
	const [error, setError] = useState("");
	const [loading, setLoading] = useState(false);
	const [timer, setTimer] = useState(5);

	const [form] = Form.useForm();

	useEffect(() => {
		let interval: NodeJS.Timeout;
		if (submitted) {
			setTimer(5);
			interval = setInterval(() => {
				setTimer((prev) => {
					if (prev <= 1) {
						clearInterval(interval);
						window.location.reload();
						return 0;
					}
					return prev - 1;
				});
			}, 1000);
		}
		return () => clearInterval(interval);
	}, [submitted]);

	const handleSubmit = async (values: { email: string }) => {
		const { email } = values;
		setLoading(true);
		setError("");

		try {
			await fetch(
				"https://sreshto.com/wp-json/lead-capture/v1/subscribe",
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
					},
					body: JSON.stringify({ email }),
				}
			);

			await apiFetch({
				path: "/swift-coupons/v1/rating-unlock",
				method: "GET",
			});

			setSubmitted(true);
		} catch (err: any) {
			setError(__("Something went wrong. Please try again.", "swift-coupons"));
		} finally {
			setLoading(false);
		}
	};

	return (
		<div className="tw-relative tw-bg-white tw-w-full tw-max-w-lg tw-mx-auto tw-rounded-2xl tw-shadow-2xl tw-transform tw-transition-all tw-duration-300 tw-ease-in-out tw-scale-100">
			<div className="tw-p-8 md:tw-p-10 tw-text-center">
				<div className="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-bg-blue-100 tw-rounded-full tw-mb-5">
					<StarIcon className="tw-w-9 tw-h-9 tw-text-blue-500" />
				</div>

				{submitted ? (
					<div className="tw-flex tw-flex-col tw-items-center">
						<h2 className="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-2">
							{__("Thank You!", "swift-coupons")}
						</h2>
						<p className="tw-text-gray-600">
							{__("Your feature has been unlocked.", "swift-coupons")}
							<br />
							{__("Reload the page to use it.", "swift-coupons")}
						</p>
						<p className="tw-text-gray-500 tw-mt-2">
							{__("Reloading in", "swift-coupons")} {timer} {timer !== 1 ? __("seconds", "swift-coupons") : __("second", "swift-coupons")}.
						</p>
					</div>
				) : (
					<>
						<h2 className="tw-text-2xl tw-font-bold tw-text-gray-800 tw-mb-3">
							{__("Unlock this feature for FREE!", "swift-coupons")}
						</h2>
						<p className="tw-text-gray-600 tw-text-lg tw-mb-6 tw-leading-relaxed">
							{__("Simply leave us a review", "swift-coupons")} {" "}
							<a
								href={pluginUrl}
								target="_blank"
								rel="noopener noreferrer"
								className="tw-font-bold tw-text-blue-600 hover:tw-text-blue-700 tw-underline tw-transition-colors tw-duration-200"
							>
								{__("here", "swift-coupons")}
							</a>
						</p>
						<div className="tw-flex tw-items-center tw-my-6">
							<hr className="tw-flex-grow tw-border-t tw-border-gray-200" />
							<span className="tw-mx-4 tw-text-sm tw-font-medium tw-text-gray-400">
								{__("THEN", "swift-coupons")}
							</span>
							<hr className="tw-flex-grow tw-border-t tw-border-gray-200" />
						</div>
						<p className="tw-text-gray-600 tw-mb-4">
							{__("After leaving your review, enter your email below and we'll unlock this feature for you.", "swift-coupons")}
						</p>
						<Form
							form={form}
							layout="vertical"
							onFinish={handleSubmit}
							initialValues={{ email: "" }}
							className="tw-mt-2"
						>
							<Form.Item
								name="email"
								rules={[
									{
										required: true,
										message: __("Please enter your email address.", "swift-coupons"),
									},
									{
										type: "email",
										message: __("Please enter a valid email address.", "swift-coupons"),
									},
								]}
							>
								<Input
									type="email"
									placeholder={__("you@example.com", "swift-coupons")}
									aria-label={__("Email Address", "swift-coupons")}
									disabled={loading}
									size="large"
								/>
							</Form.Item>
							<Form.Item>
								<Button
									type="primary"
									htmlType="submit"
									loading={loading}
									block
									size="large"
								>
									{__("Submit", "swift-coupons")}
								</Button>
								<p className="tw-text-gray-400 tw-text-xs tw-mb-0">
									{__("We respect your privacy and we will not spam you.", "swift-coupons")}
								</p>
							</Form.Item>
							{error && (
								<Alert
									message={error}
									type="error"
									showIcon
									className="tw-mb-2"
								/>
							)}
						</Form>
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
