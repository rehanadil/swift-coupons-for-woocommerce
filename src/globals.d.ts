// swift-coupons global type declarations

declare global {
	var swiftCP: {
		siteUrl: string;
		isPremium: boolean;
		ratingUnlocked: boolean;
	};

	var swiftCouponSingle: {
		coupon: {
			id: number;
			code: string;
		};
		data: {
			url_apply: {
				enabled: boolean;
				code_override: string;
				redirect_to_url: string;
				custom_success_message: string;
				redirect_back_to_origin: boolean;
			};
		};
	};
}

export {}; // This file only contains global declarations
