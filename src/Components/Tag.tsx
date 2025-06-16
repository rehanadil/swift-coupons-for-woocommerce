import { FireFilled, SketchOutlined } from "@ant-design/icons";
import { __ } from "@wordpress/i18n"; // Import WordPress translation function

type Props = {
	className?: string; // Optional className for custom styling
};

const Premium = ({ className = "" }: Props) => {
	return (
		<span
			className={`tw-inline-flex tw-justify-center tw-items-center tw-gap-1 tw-text-white tw-text-[10px] tw-uppercase tw-ml-2 tw-px-[6px] tw-py-[2px] tw-rounded-md tw-bg-violet-500 ${className}`}
		>
			<SketchOutlined />
			{__("Premium", "swift-coupons")}
		</span>
	);
};

export default { Premium };
