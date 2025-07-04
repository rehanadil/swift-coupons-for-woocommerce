// Importing necessary dependencies and components
import "../../globals.d.ts";
import { useState, useEffect } from "@wordpress/element";
import { Switch, TimePicker, Row, Col, Tooltip, Checkbox } from "antd";
import dayjs, { Dayjs } from "dayjs";
import {
	CalendarOutlined,
	QuestionCircleOutlined,
	UnlockOutlined,
} from "@ant-design/icons";
import Title from "antd/es/typography/Title";
import { DatePicker } from "antd";
import { __ } from "@wordpress/i18n";
import Paragraph from "antd/es/typography/Paragraph";
import Notice from "../../Components/Notice";

const { RangePicker } = DatePicker;
const { RangePicker: TimeRangePicker } = TimePicker;

// Defining the structure of the scheduler data
type Scheduler_Data = {
	enabled: boolean; // Whether the scheduler is enabled
	start_date: string | null; // Start date of the scheduler
	end_date: string | null; // End date of the scheduler
	weekdays_enabled: boolean; // Whether weekday scheduling is enabled
	weekdays: {
		enabled: boolean; // Whether the specific weekday is enabled
		from: string | null; // Start time for the weekday
		to: string | null; // End time for the weekday
	}[];
};

// Declaring the global variable for coupon settings
declare var swiftCouponSingle: {
	data: {
		scheduler: Scheduler_Data;
	};
};

const Scheduler: React.FC = () => {
	// State to manage whether the scheduler is enabled
	const [schedulerEnabled, setSchedulerEnabled] = useState(
		swiftCouponSingle.data?.scheduler?.enabled || false
	);
	// State to manage whether weekday scheduling is enabled
	const [weekdaysEnabled, setWeekdaysEnabled] = useState(
		swiftCP.isPremium
			? swiftCouponSingle.data?.scheduler?.weekdays_enabled || false
			: false
	);
	// State to manage the date range for the scheduler
	const [dateRange, setDateRange] = useState<[Dayjs | null, Dayjs | null]>([
		swiftCouponSingle.data?.scheduler?.start_date
			? dayjs(swiftCouponSingle.data?.scheduler?.start_date)
			: null,
		swiftCouponSingle.data?.scheduler?.end_date
			? dayjs(swiftCouponSingle.data?.scheduler?.end_date)
			: null,
	]);
	// State to manage the weekday configurations
	const [weekdays, setWeekdays] = useState(
		swiftCouponSingle.data?.scheduler?.weekdays
			? swiftCouponSingle.data?.scheduler?.weekdays.map((weekday) => ({
					enabled: weekday.enabled,
					from: weekday.from ? dayjs(weekday.from, "HH:mm:ss") : null,
					to: weekday.to ? dayjs(weekday.to, "HH:mm:ss") : null,
			  }))
			: Array(7).fill({ from: null, to: null, enabled: false })
	);

	// Effect to dispatch a custom event whenever scheduler data changes
	useEffect(() => {
		const event = new CustomEvent("swiftcoupons-coupon-data-changed", {
			detail: {
				type: "scheduler",
				data: {
					enabled: schedulerEnabled,
					start_date: dateRange[0]?.format("YYYY-MM-DD") || null,
					end_date: dateRange[1]?.format("YYYY-MM-DD") || null,
					weekdays_enabled: weekdaysEnabled,
					weekdays: weekdays.map((weekday) => ({
						enabled: weekday.enabled,
						from: weekday.from?.format("HH:mm:ss") || null,
						to: weekday.to?.format("HH:mm:ss") || null,
					})),
				},
			},
		});
		window.dispatchEvent(event);
	}, [schedulerEnabled, weekdaysEnabled, weekdays, dateRange]);

	// Function to handle changes in the date range
	const handleDateChange = (dates: [Dayjs | null, Dayjs | null]) => {
		setDateRange(dates);
	};

	// Function to handle changes in weekday configurations
	const handleWeekdayChange = (
		index: number,
		keys: string[],
		values: (Dayjs | null | boolean)[]
	) => {
		const newWeekdays = [...weekdays];
		newWeekdays[index] = { ...newWeekdays[index] };

		keys.forEach((key, i) => {
			if (key === "from" || key === "to" || key === "enabled") {
				newWeekdays[index][key] = values[i];
			}
		});

		setWeekdays(newWeekdays);
	};

	return (
		<div className="tw-px-4 tw-flex tw-flex-col tw-gap-2">
			{/* Header section with title and description */}
			<div className="tw-flex tw-flex-col">
				<Title level={3} className="tw-mt-4">
					{__("Scheduler", "swift-coupons-for-woocommerce")}
				</Title>
				<Paragraph>
					{__(
						"The Scheduler feature allows you to define specific time periods and days of the week during which a coupon is valid. This provides precise control over coupon availability, ensuring it aligns with your promotional strategies.",
						"swift-coupons-for-woocommerce"
					)}
				</Paragraph>
			</div>

			{/* Main content section */}
			<div className="tw-flex tw-flex-col tw-gap-6">
				{/* Scheduler enable toggle */}
				<div className="tw-flex tw-flex-col tw-gap-3">
					<div className="tw-flex tw-gap-1 tw-items-center">
						<Switch
							checked={schedulerEnabled}
							onChange={(checked) => setSchedulerEnabled(checked)}
						/>
						<span
							className="tw-text-sm tw-cursor-pointer tw-font-semibold"
							onClick={() =>
								setSchedulerEnabled(!schedulerEnabled)
							}
						>
							{__(
								"Enable Scheduler",
								"swift-coupons-for-woocommerce"
							)}
						</span>
						<Tooltip
							placement="bottom"
							arrow
							title={__(
								"The scheduler gives you fine grained control over when this coupon is valid. Choose the start date & time, along with the end date & time. Optionally, show a WooCommerce notification message when the coupon is attempted to be applied outside of the allowed schedule.",
								"swift-coupons-for-woocommerce"
							)}
							className="tw-cursor-pointer"
						>
							<QuestionCircleOutlined />
						</Tooltip>
					</div>

					{/* Date range picker */}
					<Row
						gutter={16}
						className={
							schedulerEnabled
								? ""
								: "tw-opacity-70 tw-pointer-events-none"
						}
					>
						<Col span={24}>
							<div className="tw-flex tw-flex-col tw-gap-1">
								<div className="tw-flex tw-justify-between">
									<label>
										{__(
											"Start Date",
											"swift-coupons-for-woocommerce"
										)}
									</label>
									<label>
										{__(
											"End Date",
											"swift-coupons-for-woocommerce"
										)}
									</label>
								</div>
								<RangePicker
									defaultValue={dateRange}
									onChange={(dates) => {
										if (dates)
											handleDateChange(
												dates as [
													Dayjs | null,
													Dayjs | null,
												]
											);
									}}
								/>
							</div>
						</Col>
					</Row>
				</div>

				<div className="tw-flex tw-flex-col tw-gap-4">
					{/* Premium message if not premium */}
					<Notice.Premium
						unlocked={swiftCP.isPremium}
						refer="weekdays-scheduling"
						icon={
							swiftCP.isPremium ? (
								<UnlockOutlined
									style={{
										fontSize: 24,
										color: "#06d9d9",
									}}
								/>
							) : (
								<CalendarOutlined
									style={{
										fontSize: 24,
										color: "#D97706",
									}}
								/>
							)
						}
						title={__(
							"Weekdays Scheduling is a Premium Feature",
							"swift-coupons-for-woocommerce"
						)}
						description={__(
							swiftCP.isPremium
								? "Congratulations! You now have access to this feature. Enjoy!"
								: "Unlock advanced scheduling by upgrading to Swift Coupons Premium. Control coupon validity by day and time!",
							"swift-coupons-for-woocommerce"
						)}
					/>

					{/* Weekday scheduling toggle */}
					<div className="tw-px-4">
						<Switch
							checked={weekdaysEnabled}
							onChange={(checked) => setWeekdaysEnabled(checked)}
							className={
								schedulerEnabled
									? ""
									: "tw-opacity-70 tw-pointer-events-none"
							}
							disabled={!swiftCP.isPremium}
						/>
						<span
							className={`tw-ml-2 tw-text-sm tw-cursor-pointer tw-font-semibold ${
								schedulerEnabled && swiftCP.isPremium
									? ""
									: "tw-opacity-70 tw-pointer-events-none"
							}`}
							onClick={() =>
								swiftCP.isPremium &&
								setWeekdaysEnabled(!weekdaysEnabled)
							}
						>
							{__(
								"Enable Weekdays Scheduling",
								"swift-coupons-for-woocommerce"
							)}
						</span>

						{/* Weekday configurations */}
						<div
							className={
								weekdaysEnabled &&
								schedulerEnabled &&
								swiftCP.isPremium
									? ""
									: "tw-opacity-70 tw-pointer-events-none"
							}
						>
							{[
								__("Monday", "swift-coupons-for-woocommerce"),
								__("Tuesday", "swift-coupons-for-woocommerce"),
								__(
									"Wednesday",
									"swift-coupons-for-woocommerce"
								),
								__("Thursday", "swift-coupons-for-woocommerce"),
								__("Friday", "swift-coupons-for-woocommerce"),
								__("Saturday", "swift-coupons-for-woocommerce"),
								__("Sunday", "swift-coupons-for-woocommerce"),
							].map((day, index) => (
								<Row
									gutter={16}
									className="tw-my-4 tw-items-center tw-justify-between"
									key={index}
								>
									<Col span={6}>
										<Checkbox
											checked={weekdays[index].enabled}
											onChange={(e) =>
												handleWeekdayChange(
													index,
													["enabled"],
													[e.target.checked]
												)
											}
											disabled={!swiftCP.isPremium}
										>
											{day}
										</Checkbox>
									</Col>
									<Col span={18}>
										<TimeRangePicker
											defaultValue={[
												weekdays[index].from,
												weekdays[index].to,
											]}
											onChange={(times) => {
												handleWeekdayChange(
													index,
													["from", "to"],
													[
														times?.[0] || null,
														times?.[1] || null,
													]
												);
											}}
											format="HH:mm:ss"
											placeholder={[
												__(
													"From",
													"swift-coupons-for-woocommerce"
												),
												__(
													"To",
													"swift-coupons-for-woocommerce"
												),
											]}
											disabled={
												!weekdays[index].enabled ||
												!swiftCP.isPremium
											}
										/>
									</Col>
								</Row>
							))}
						</div>
					</div>
				</div>
			</div>
		</div>
	);
};

export default Scheduler;
