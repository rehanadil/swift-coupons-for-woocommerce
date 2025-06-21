import React from "react";
import { createRoot } from "react-dom/client";
import { StyleProvider } from "@ant-design/cssinjs";
import {
	Button,
	Card,
	Col,
	Row,
	Typography,
	Space,
	Avatar,
	ConfigProvider,
} from "antd";
import {
	GiftOutlined,
	CheckCircleOutlined,
	CalendarOutlined,
	LinkOutlined,
	RocketOutlined,
	ArrowRightOutlined,
	RetweetOutlined,
	ControlOutlined,
} from "@ant-design/icons";
import { __ } from "@wordpress/i18n";
import Logo from "../Components/Logo";

type FeatureCardProps = {
	icon: React.ReactNode;
	title: string;
	description: string;
	color: string;
};

const { Text } = Typography;

const features = [
	{
		icon: <GiftOutlined className="tw-text-2xl" />,
		title: __("Buy X, Get X Deals", "swift-coupons-for-woocommerce"),
		description: __(
			"Create powerful BOGO-style discounts and other advanced product offers effortlessly.",
			"swift-coupons-for-woocommerce"
		),
		color: "#6c63ff",
	},
	{
		icon: <ControlOutlined className="tw-text-2xl" />,
		title: __("Cart Qualifiers", "swift-coupons-for-woocommerce"),
		description: __(
			"Apply coupons only when specific conditions are met, like cart quantity, cart total, user role, items in cart, etc.",
			"swift-coupons-for-woocommerce"
		),
		color: "#34d399",
	},
	{
		icon: <CalendarOutlined className="tw-text-2xl" />,
		title: __("Advanced Scheduling", "swift-coupons-for-woocommerce"),
		description: __(
			"Set precise start and end dates, or schedule coupons to be active on specific days of the week.",
			"swift-coupons-for-woocommerce"
		),
		color: "#fbbf24",
	},
	{
		icon: <RetweetOutlined className="tw-text-2xl" />,
		title: __("Auto-Apply Coupons", "swift-coupons-for-woocommerce"),
		description: __(
			"Improve user experience by automatically applying the best possible coupon at checkout.",
			"swift-coupons-for-woocommerce"
		),
		color: "#f87171",
	},
	{
		icon: <LinkOutlined className="tw-text-2xl" />,
		title: __("URL Coupon Sharing", "swift-coupons-for-woocommerce"),
		description: __(
			"Generate user-friendly links that automatically apply a coupon when a customer visits your store.",
			"swift-coupons-for-woocommerce"
		),
		color: "#60a5fa",
	},
	{
		icon: <RocketOutlined className="tw-text-2xl" />,
		title: __("And Much More...", "swift-coupons-for-woocommerce"),
		description: __(
			"Explore a rich set of features designed to supercharge your WooCommerce promotions.",
			"swift-coupons-for-woocommerce"
		),
		color: "#a78bfa",
	},
];

const FeatureCard = ({ icon, title, description, color }: FeatureCardProps) => (
	<Card
		bordered={false}
		className="tw-h-full tw-text-center tw-shadow-none hover:tw-shadow-lg tw-transition-shadow tw-duration-300 tw-bg-white"
	>
		<Space direction="vertical" size="middle" className="tw-w-full">
			<Avatar
				size={64}
				icon={icon}
				style={{ backgroundColor: color, color: "#fff" }}
				className="tw-flex tw-items-center tw-justify-center"
			/>
			<Typography.Title level={5} className="tw-font-semibold">
				{title}
			</Typography.Title>
			<Typography.Paragraph type="secondary" className="tw-min-h-[60px]">
				{description}
			</Typography.Paragraph>
		</Space>
	</Card>
);

const Welcome = () => {
	const handleGoToCoupons = () => {
		window.location.href = "/wp-admin/edit.php?post_type=shop_coupon";
	};

	return (
		<>
			<div className="tw-min-h-screen tw-bg-gray-50 tw-p-4 sm:tw-p-8 tw-pb-40">
				{/* Padding bottom to clear sticky footer */}
				<div className="tw-max-w-5xl tw-mx-auto tw-w-full tw-bg-white tw-rounded-2xl tw-shadow-xl tw-overflow-hidden">
					{/* Header Section */}
					<div className="tw-p-8 md:tw-p-12 tw-text-center tw-bg-gradient-to-br tw-from-indigo-500 tw-to-violet-600">
						<Space direction="vertical" size="small">
							<Logo className="tw-text-slate-100 tw-text-5xl" />
							<Typography.Title
								level={1}
								className="!tw-text-white tw-font-bold"
							>
								{__(
									"Welcome to Swift Coupons!",
									"swift-coupons-for-woocommerce"
								)}
							</Typography.Title>
							<Typography.Paragraph className="!tw-text-lg !tw-text-indigo-100">
								{__(
									"You've unlocked a powerful new way to manage WooCommerce coupons.",
									"swift-coupons-for-woocommerce"
								)}
							</Typography.Paragraph>
						</Space>
					</div>

					<div className="tw-flex tw-flex-col tw-gap-6 tw-items-center tw-p-8 md:tw-p-12">
						<div className="tw-flex tw-flex-col tw-gap-3 tw-items-center tw-justify-center">
							<Typography.Title
								level={4}
								className="!tw-my-0 tw-font-normal tw-text-gray-800"
							>
								{__(
									"Ready to get started?",
									"swift-coupons-for-woocommerce"
								)}
							</Typography.Title>
							<Button
								type="primary"
								size="large"
								icon={<ArrowRightOutlined />}
								onClick={handleGoToCoupons}
								className="animate-subtle-bounce tw-h-14 tw-px-8 tw-text-lg tw-font-semibold tw-rounded-lg tw-transition-shadow tw-duration-300"
							>
								{__(
									"Manage Your Coupons",
									"swift-coupons-for-woocommerce"
								)}
							</Button>
						</div>

						{/* Features Intro */}
						<div className="tw-flex tw-flex-col tw-justify-center tw-items-center">
							<Typography.Title level={3}>
								{__(
									"Unlock Your Promotional Superpowers",
									"swift-coupons-for-woocommerce"
								)}
							</Typography.Title>
							<Typography.Paragraph
								type="secondary"
								className="tw-text-lg"
							>
								{__(
									"Here are some of the key features now at your fingertips:",
									"swift-coupons-for-woocommerce"
								)}
							</Typography.Paragraph>
						</div>

						{/* Features Grid */}
						<Row gutter={[24, 24]}>
							{features.map((feature, index) => (
								<Col xs={24} sm={12} md={8} key={index}>
									<FeatureCard {...feature} />
								</Col>
							))}
						</Row>
					</div>
				</div>

				<Text
					disabled
					className="tw-text-xs tw-text-center tw-block tw-mt-6 tw-tracking-wide"
				>
					{__("Powered by", "swift-coupons-for-woocommerce")}{" "}
					<a
						href="https://sreshto.com"
						target="_blank"
						rel="noopener noreferrer"
						className="tw-underline hover:tw-text-violet-700"
					>
						Sreshto
					</a>
				</Text>
			</div>
		</>
	);
};

const container = document.getElementById("swift-coupons-welcome-root");
if (container) {
	createRoot(container).render(
		<StyleProvider hashPriority="high">
			<ConfigProvider
				theme={{
					token: {
						zIndexPopupBase: 1001, // Set base z-index for popups
						colorPrimary: "#8b5cf6", // Set primary color
					},
				}}
			>
				<Welcome />
			</ConfigProvider>
		</StyleProvider>
	);
}
