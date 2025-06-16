// Importing necessary libraries and components
import "../../globals.d.ts";
import React, { useState } from "react";
import { Form, Select, InputNumber, Button, Table, Switch, Modal } from "antd";
import {
	PlusOutlined,
	DeleteOutlined,
	GiftOutlined,
	LockFilled,
} from "@ant-design/icons";
import debounce from "lodash.debounce";
import apiFetch from "@wordpress/api-fetch";
import { __ } from "@wordpress/i18n";
import Title from "antd/es/typography/Title";
import Paragraph from "antd/es/typography/Paragraph";
import Notice from "../../Components/Notice";
import BetterSelect from "../../Components/BetterSelect";
import Tag from "../../Components/Tag";

// Defining the structure of a BuyItem
// Represents an item that needs to be purchased to qualify for the deal
type BuyItem = {
	key: number; // Unique identifier for the item
	type: "product" | "category"; // Type of the item
	id: number; // ID of the product or category
	name: string; // Name of the product or category
	quantity: {
		value: number; // Quantity required to qualify
	};
};

// Defining the structure of a GetItem
// Represents an item that the customer gets as part of the deal
type GetItem = {
	key: number; // Unique identifier for the item
	type: "product" | "category"; // Type of the item
	id: number; // ID of the product or category
	name: string; // Name of the product or category
	quantity: {
		value: number; // Quantity of the item to be given
	};
	discount: {
		type: string; // Type of discount (e.g., percentage, fixed amount)
		value: number; // Value of the discount
	};
};

// Structure of the BXGX data
// Contains the configuration for the Buy X Get X deal
type BXGX_Data = {
	enabled: boolean; // Whether the feature is enabled
	buy: {
		match: "all" | "any"; // Match type for buy items
		items: BuyItem[]; // List of buy items
	};
	get: {
		apply: "all" | "cheapest" | "most_expensive" | "random"; // Application type for get items
		items: GetItem[]; // List of get items
	};
};

// Declaring a global variable for initial data
// This is provided by the WordPress backend
declare var swiftCouponSingle: {
	data: {
		bxgx: BXGX_Data;
	};
};

// Type for search results used in dropdowns
type SearchResult = { label: string; value: number };

const { Option } = Select; // Destructuring Option from Select for convenience

const BXGX: React.FC = () => {
	// State variables to manage the component's data and UI
	const [buyItems, setBuyItems] = useState<BuyItem[]>(
		swiftCouponSingle.data?.bxgx?.buy?.items || [] // Initializing with backend data
	);
	const [getItems, setGetItems] = useState<GetItem[]>(
		swiftCouponSingle.data?.bxgx?.get?.items || [] // Initializing with backend data
	);
	const [loading, setLoading] = useState(false); // Loading state for API calls
	const [productOptions, setProductOptions] = useState<SearchResult[]>(
		(swiftCouponSingle.data?.bxgx?.buy?.items || []).map((item) => ({
			label: item.name,
			value: item.id,
		})) // Preloading product options
	);
	const [categoryOptions, setCategoryOptions] = useState<SearchResult[]>([]); // Options for category dropdown
	const [buyMatchType, setBuyMatchType] = useState<"all" | "any">(
		swiftCouponSingle.data?.bxgx?.buy?.match || "all" // Initializing match type
	);
	const [getApplyType, setGetApplyType] = useState<
		"all" | "cheapest" | "most_expensive" | "random"
	>(swiftCouponSingle.data?.bxgx?.get?.apply || "all"); // Initializing apply type
	const [isEnabled, setIsEnabled] = useState<boolean>(
		swiftCouponSingle.data?.bxgx?.enabled || false // Initializing enabled state
	);
	const [premiumModalOpen, setPremiumModalOpen] = useState(false);

	// Effect to dispatch custom event when data changes
	React.useEffect(() => {
		const event = new CustomEvent("swiftcou-coupon-data-changed", {
			detail: {
				type: "bxgx",
				data: {
					enabled: isEnabled,
					buy: {
						match: buyMatchType,
						items: buyItems,
					},
					get: {
						apply: getApplyType,
						items: getItems,
					},
				},
			},
		});
		window.dispatchEvent(event); // Dispatching the event
	}, [buyItems, getItems, isEnabled, buyMatchType, getApplyType]);

	// Function to fetch products based on search input
	const fetchProducts = debounce(async (search: string) => {
		if (!search) return; // Do nothing if search is empty
		setLoading(true); // Set loading state
		try {
			const data = await apiFetch<{
				results: { label: string; value: number }[];
			}>({
				path: `/swift-coupons/v1/products/search?query=${search}`, // API endpoint
			});
			setProductOptions(
				data.results.map((item) => ({
					label: item.label,
					value: item.value,
				})) // Updating product options
			);
		} catch (error) {
			console.error(`Error fetching products:`, error); // Log errors
		} finally {
			setLoading(false); // Reset loading state
		}
	}, 500); // Debounce to limit API calls

	// Function to fetch categories based on search input
	const fetchCategories = debounce(async (search: string) => {
		if (!search) return; // Do nothing if search is empty
		setLoading(true); // Set loading state
		try {
			const data = await apiFetch<{
				results: { label: string; value: number }[];
			}>({
				path: `/swift-coupons/v1/categories/search?query=${search}`, // API endpoint
			});
			setCategoryOptions(
				data.results.map((item) => ({
					label: item.label,
					value: item.value,
				})) // Updating category options
			);
		} catch (error) {
			console.error(`Error fetching categories:`, error); // Log errors
		} finally {
			setLoading(false); // Reset loading state
		}
	}, 500); // Debounce to limit API calls

	// Function to add a new Get Item
	const handleAddGetItem = () => {
		setGetItems((prevGetItems) => [
			...prevGetItems,
			{
				key: Date.now(), // Unique key
				type: "product", // Default type
				id: 0, // Default ID
				name: "", // Default name
				quantity: {
					value: 1, // Default quantity
				},
				discount: {
					type: "", // Default discount type
					value: 0, // Default discount value
				},
			},
		]);
	};

	// Function to add a new Buy Item
	const handleAddBuyItem = () => {
		setBuyItems((prevBuyItems) => [
			...prevBuyItems,
			{
				key: Date.now(), // Unique key
				type: "product", // Default type
				id: 0, // Default ID
				name: "", // Default name
				quantity: {
					value: 1, // Default quantity
				},
			},
		]);
	};

	// Function to remove a Get Item by key
	const handleRemoveGetItem = (key: number) => {
		setGetItems(
			(prevGetItems) => prevGetItems.filter((item) => item.key !== key) // Filter out the item
		);
	};

	// Function to remove a Buy Item by key
	const handleRemoveBuyItem = (key: number) => {
		setBuyItems(
			(prevBuyItems) => prevBuyItems.filter((item) => item.key !== key) // Filter out the item
		);
	};

	// Function to update a Get Item's field
	const handleGetItemChange = (
		key: number,
		field: keyof GetItem,
		value: any
	) => {
		setGetItems((prevGetItems) =>
			prevGetItems.map(
				(item) =>
					item.key === key ? { ...item, [field]: value } : item // Update the field
			)
		);
	};

	// Function to update a Buy Item's field
	const handleBuyItemChange = (
		key: number,
		field: keyof BuyItem,
		value: any
	) => {
		setBuyItems((prevBuyItems) =>
			prevBuyItems.map(
				(item) =>
					item.key === key ? { ...item, [field]: value } : item // Update the field
			)
		);
	};

	// Handler for Buy Match Type ("all" | "any")
	const handleBuyMatchTypeChange = (value: "all" | "any") => {
		if (value === "any" && !swiftCP.isPremium) {
			setPremiumModalOpen(true);
			return;
		}
		setBuyMatchType(value);
	};

	// Handler for Buy Item Type ("product" | "category")
	const handleBuyItemTypeChange = (
		key: number,
		value: "product" | "category"
	) => {
		if (value === "category" && !swiftCP.isPremium) {
			setPremiumModalOpen(true);
			return;
		}
		handleBuyItemChange(key, "type", value);
	};

	// Handler for Get Item Type ("product" | "category")
	const handleGetItemTypeChange = (
		key: number,
		value: "product" | "category"
	) => {
		if (value === "category" && !swiftCP.isPremium) {
			setPremiumModalOpen(true);
			return;
		}
		handleGetItemChange(key, "type", value);
	};

	// Columns configuration for the Get Items table
	const getColumns: Array<{
		title: string; // Column title
		dataIndex?: keyof GetItem; // Data field
		key: string; // Unique key
		fixed?: boolean | "right" | "left"; // Fixed position
		width?: number | string; // Column width
		render: (_: any, record: GetItem) => JSX.Element; // Render function
	}> = [
		{
			title: __("Type", "swift-coupons"),
			dataIndex: "type",
			key: "type",
			width: 150, // Set the width of the column
			render: (_: any, record: GetItem) => (
				<Select
					value={record.type}
					onChange={(value) =>
						handleGetItemTypeChange(record.key, value)
					}
				>
					<Option value="product">
						{__("Product", "swift-coupons")}
					</Option>
					<Option value="category">
						{__("Category", "swift-coupons")}
					</Option>
				</Select>
			),
		},
		{
			title: __("Name", "swift-coupons"),
			dataIndex: "name",
			key: "name",
			width: 200, // Set the width of the column
			render: (_: any, record: GetItem) => (
				<Select
					showSearch
					value={record.name}
					placeholder={`Search ${record.type}`}
					filterOption={false}
					onSearch={(search) =>
						record.type === "category"
							? fetchCategories(search)
							: fetchProducts(search)
					}
					onChange={(value, opt: any) => {
						handleGetItemChange(record.key, "id", value);
						handleGetItemChange(record.key, "name", opt.children);
					}}
					loading={loading}
					style={{ width: "100%" }}
				>
					{record.type === "category"
						? categoryOptions.map((option) => (
								<Option key={option.value} value={option.value}>
									{option.label}
								</Option>
						  ))
						: productOptions.map((option) => (
								<Option key={option.value} value={option.value}>
									{option.label}
								</Option>
						  ))}
				</Select>
			),
		},
		{
			title: __("Quantity", "swift-coupons"),
			dataIndex: "quantity",
			key: "quantity",
			width: 50, // Set the width of the column
			render: (_: any, record: GetItem) => (
				<InputNumber
					min={1}
					value={record.quantity.value}
					onChange={(value) =>
						handleGetItemChange(record.key, "quantity", {
							value: value,
						})
					}
				/>
			),
		},
		{
			title: __("Discount", "swift-coupons"),
			dataIndex: "discount",
			key: "discount",
			width: 100,
			render: (_: any, record: GetItem) => (
				<div className="tw-flex tw-flex-col tw-gap-1">
					<Select
						value={record.discount.type}
						onChange={(value) =>
							handleGetItemChange(record.key, "discount", {
								...record.discount,
								type: value,
							})
						}
					>
						<Option value="override_price">
							{__("Override Price", "swift-coupons")}
						</Option>
						<Option value="percent">
							{__("Percentage Discount", "swift-coupons")}
						</Option>
						<Option value="fixed">
							{__("Fixed Discount", "swift-coupons")}
						</Option>
					</Select>
					<InputNumber
						min={0}
						value={record.discount.value}
						onChange={(value) =>
							handleGetItemChange(record.key, "discount", {
								...record.discount,
								value,
							})
						}
						className="tw-w-full"
					/>
				</div>
			),
		},
		{
			title: __("Action", "swift-coupons"),
			key: "action",
			width: 50, // Set the width of the column
			render: (_: any, record: GetItem) => (
				<Button
					type="link"
					danger
					onClick={() => handleRemoveGetItem(record.key)}
				>
					<DeleteOutlined />
				</Button>
			),
		},
	];

	// Columns configuration for the Buy Items table
	const buyColumns: Array<{
		title: string; // Column title
		dataIndex?: keyof BuyItem; // Data field
		key: string; // Unique key
		fixed?: boolean | "right" | "left"; // Fixed position
		width?: number | string; // Column width
		render: (_: any, record: BuyItem) => JSX.Element; // Render function
	}> = [
		{
			title: __("Type", "swift-coupons"),
			dataIndex: "type",
			key: "type",
			width: 150, // Set the width of the column
			render: (_: any, record: BuyItem) => (
				<BetterSelect
					value={record.type}
					onChange={(value: "product" | "category") =>
						handleBuyItemTypeChange(record.key, value)
					}
					style={{ width: "100%" }}
					descriptionIndent={false}
					options={[
						{
							value: "product",
							label: __("Product", "swift-coupons"),
						},
						{
							value: "category",
							label: __("Category", "swift-coupons"),
							tags: [<Tag.Premium />],
						},
					]}
				/>
			),
		},
		{
			title: __("Name", "swift-coupons"),
			dataIndex: "name",
			key: "name",
			width: 300, // Set the width of the column
			render: (_: any, record: BuyItem) => (
				<Select
					showSearch
					value={record.name}
					placeholder={`Search ${record.type}`}
					filterOption={false}
					onSearch={(search) =>
						record.type === "category"
							? fetchCategories(search)
							: fetchProducts(search)
					}
					onChange={(value, opt: any) => {
						handleBuyItemChange(record.key, "id", value);
						handleBuyItemChange(record.key, "name", opt.children);
					}}
					loading={loading}
					style={{ width: "100%" }}
				>
					{record.type === "category"
						? categoryOptions.map((option) => (
								<Option key={option.value} value={option.value}>
									{option.label}
								</Option>
						  ))
						: productOptions.map((option) => (
								<Option key={option.value} value={option.value}>
									{option.label}
								</Option>
						  ))}
				</Select>
			),
		},
		{
			title: __("Quantity", "swift-coupons"),
			dataIndex: "quantity",
			key: "quantity",
			width: 50, // Set the width of the column
			render: (_: any, record: BuyItem) => (
				<InputNumber
					min={1}
					value={record.quantity.value}
					onChange={(value) =>
						handleBuyItemChange(record.key, "quantity", {
							value,
						})
					}
				/>
			),
		},
		{
			title: __("Action", "swift-coupons"),
			key: "action",
			width: 50, // Set the width of the column
			render: (_: any, record: BuyItem) => (
				<Button
					type="link"
					danger
					onClick={() => handleRemoveBuyItem(record.key)}
				>
					<DeleteOutlined />
				</Button>
			),
		},
	];

	return (
		<>
			{/* Header section with title and description */}
			<div className="tw-px-4 tw-flex tw-flex-col">
				<Title level={3} className="tw-mt-4">
					{__("Buy X Get X (Deals)", "swift-coupons")}
				</Title>
				<Paragraph>
					{__(
						"The Buy X Get X feature enables you to create promotional deals where customers can purchase specific items and receive additional items as part of the offer. This feature provides flexibility in defining the conditions for both the items to buy and the items to get, allowing you to tailor promotions to suit your business needs and drive customer engagement.",
						"swift-coupons"
					)}
				</Paragraph>
			</div>

			{/* Toggle switch to enable or disable the feature */}
			<div className="tw-pt-3 tw-px-4 tw-flex tw-items-center">
				<Switch
					checked={isEnabled}
					onChange={(checked) => setIsEnabled(checked)}
				/>
				<span
					className="tw-ml-2 tw-text-sm tw-cursor-pointer tw-font-semibold"
					onClick={() => setIsEnabled(!isEnabled)}
				>
					{__("Enable BXGX", "swift-coupons")}
				</span>
			</div>

			{/* Main content section */}
			<div
				className={
					isEnabled
						? "gap"
						: "tw-opacity-50 tw-pointer-events-none tw-gap-8"
				}
			>
				{/* Buy Items section */}
				<div>
					<h3 className="tw-px-4 tw-font-medium">
						{__("Buy Items", "swift-coupons")}
						<BetterSelect
							value={buyMatchType}
							onChange={handleBuyMatchTypeChange}
							className="tw-ml-3 tw-w-48"
							descriptionIndent={false}
							options={[
								{
									value: "all",
									label: __("Match All", "swift-coupons"),
									description: __(
										"All items must be purchased to qualify for the deal.",
										"swift-coupons"
									),
								},
								{
									value: "any",
									label: __("Match Any", "swift-coupons"),
									description: __(
										"Any one of the items can be purchased to qualify for the deal.",
										"swift-coupons"
									),
									tags: [<Tag.Premium />],
								},
							]}
						/>
					</h3>
					<Table
						columns={buyColumns}
						dataSource={buyItems}
						pagination={false}
						rowKey="key"
						tableLayout="auto"
					/>
					<Button
						type="dashed"
						onClick={handleAddBuyItem}
						block
						className="tw-px-4 tw-m-3"
					>
						<PlusOutlined /> {__("Add new", "swift-coupons")}
					</Button>
				</div>

				{/* Get Items section */}
				<div className="tw-mt-4">
					<h3 className="tw-px-4 tw-font-medium">
						{__("Get Items", "swift-coupons")}
						<Select
							value={getApplyType}
							onChange={(value) => setGetApplyType(value)}
							className="tw-ml-3 tw-w-40"
						>
							<Option value="all">
								{__("Apply All", "swift-coupons")}
							</Option>
						</Select>
					</h3>
					<Table
						columns={getColumns}
						dataSource={getItems}
						pagination={false}
						rowKey="key"
						tableLayout="auto"
					/>
					<Button
						type="dashed"
						onClick={handleAddGetItem}
						block
						className="tw-m-3"
					>
						<PlusOutlined /> {__("Add new", "swift-coupons")}
					</Button>
				</div>
			</div>

			<Modal
				open={premiumModalOpen}
				centered
				footer={null}
				onCancel={() => setPremiumModalOpen(false)}
				width={420}
				styles={{
					content: { padding: 0 },
				}}
			>
				<Notice.Premium
					modal={true}
					refer="feature-bxgx"
					icon={
						<GiftOutlined
							style={{ fontSize: 48, color: "#D97706" }}
						/>
					}
					className="tw-rounded-lg"
				/>
			</Modal>
		</>
	);
};

export default BXGX;
