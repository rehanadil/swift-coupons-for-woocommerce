<?php
namespace Sreshto\SwiftCoupons;

/**
 * Class Scripts
 *
 * Handles the registration and enqueueing of scripts for the Swift Coupons plugin.
 * Implements a singleton pattern to ensure a single instance of the class.
 *
 * @package Sreshto\SwiftCoupons
 */
class Scripts
{
	/**
	 * The singleton instance of the class.
	 *
	 * @var Scripts|null
	 */
	public static $instance = null;

	public $asset_info = [];

	/**
	 * Constructor.
	 * Private to enforce singleton pattern.
	 *
	 * @author Rehan Adil
	 */
	private function __construct()
	{
		// Hook to enqueue admin scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
	}

	/**
	 * Get the singleton instance of the class.
	 *
	 * @return Scripts The singleton instance of the class.
	 * @author Rehan Adil
	 */
	public static function instance()
	{
		if ( is_null( self::$instance ) )
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register all plugin scripts and styles with WordPress.
	 *
	 * @return void
	 */
	public function register_admin_assets()
	{
		$this->get_asset_info();

		// Register scripts
		wp_register_script(
			'swiftcoupons-tabs',
			SWIFT_COUPONS_BASE_URL . '/assets/js/tabs/index.js',
			$this->asset_info[ 'tabs' ][ 'dependencies' ],
			$this->asset_info[ 'tabs' ][ 'version' ],
			true,
		);
		wp_register_script(
			'swiftcoupons-welcome',
			SWIFT_COUPONS_BASE_URL . '/assets/js/welcome/index.js',
			$this->asset_info[ 'welcome' ][ 'dependencies' ],
			$this->asset_info[ 'welcome' ][ 'version' ],
			true,
		);

		// Register styles
		wp_register_style(
			'swiftcoupons-main',
			SWIFT_COUPONS_BASE_URL . '/assets/css/style.css',
			[],
			filemtime( SWIFT_COUPONS_BASE_PATH . '/assets/css/style.css' ),
		);
		wp_register_style(
			'swiftcoupons-custom',
			SWIFT_COUPONS_BASE_URL . '/assets/css/custom.css',
			[],
			filemtime( SWIFT_COUPONS_BASE_PATH . '/assets/css/custom.css' ),
		);
	}

	/**
	 * Register and enqueue scripts for the plugin.
	 * Also localizes data for coupon-related pages.
	 *
	 * @return void
	 * @author Rehan Adil
	 */
	public function enqueue_admin_assets()
	{
		$this->register_admin_assets();

		wp_enqueue_style( 'swiftcoupons-main' );
		wp_enqueue_style( 'swiftcoupons-custom' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'swift-coupons' )
		{
			wp_enqueue_script( 'swiftcoupons-welcome' );
			wp_set_script_translations(
				'swiftcoupons-welcome',
				'swift-coupons-for-woocommerce',
				plugin_dir_path( SWIFT_COUPONS_BASE_FILE ) . 'languages'
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_add_page = isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'shop_coupon';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_edit_page = ! $is_add_page && isset( $_GET[ 'post' ] ) && get_post_type( absint( wp_unslash( $_GET[ 'post' ] ) ) ) === 'shop_coupon';

		// Coupon-related admin pages
		$is_coupon_page = $is_add_page || $is_edit_page;

		if ( $is_coupon_page )
		{
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$coupon_id = isset( $_GET[ 'post' ] ) ? absint( $_GET[ 'post' ] ) : 0;
			$coupon    = $coupon_id > 0 ? new \WC_Coupon( $coupon_id ) : null;

			$coupon_code          = $coupon ? $coupon->get_code() : '';
			$coupon_discount_type = $coupon ? $coupon->get_discount_type() : '';
			$coupon_id            = $coupon ? $coupon->get_id() : 0;

			$single_coupon_data = $coupon ? [ 
				'qualifiers' => get_post_meta( $coupon_id, 'swiftcoupons_qualifiers', true ) ?: [],
				'bxgx'       => get_post_meta( $coupon_id, 'swiftcoupons_bxgx', true ) ?: [],
				'scheduler'  => get_post_meta( $coupon_id, 'swiftcoupons_scheduler', true ) ?: [],
				'url_apply'  => get_post_meta( $coupon_id, 'swiftcoupons_url_apply', true ) ?: [],
				'auto_apply' => get_post_meta( $coupon_id, 'swiftcoupons_auto_apply', true ) ?: [],
			] : [];

			wp_enqueue_script( 'swiftcoupons-tabs' );

			wp_set_script_translations(
				'swiftcoupons-tabs',
				'swift-coupons-for-woocommerce',
				plugin_dir_path( SWIFT_COUPONS_BASE_FILE ) . 'languages'
			);

			wp_localize_script( 'swiftcoupons-tabs', 'swiftCP', [ 
				'siteUrl'   => esc_url( site_url() ),
				'isPremium' => boolval( apply_filters( 'swift-coupons/is-premium', false ) ),
			] );

			wp_localize_script( 'swiftcoupons-tabs', 'swiftCouponSingle', [ 
				'coupon'    => [ 
					'code' => esc_html( $coupon_code ),
					'id'   => absint( $coupon_id ),
					'type' => esc_html( $coupon_discount_type ),
				],
				'data'      => Utilities::esc_array( $single_coupon_data ),
				'settings'  => [ 
					'qualifiers' => [ 
						'rules' => Utilities::esc_array( apply_filters( 'swift-coupons/qualifier-rules-array', [] ) ),
					],
				],
				'pluginUrl' => SWIFT_COUPONS_BASE_URL,
			] );
		}
	}

	private function get_asset_info()
	{
		$tabs_asset_file    = include( SWIFT_COUPONS_BASE_PATH . 'assets/js/tabs/index.asset.php' );
		$welcome_asset_file = include( SWIFT_COUPONS_BASE_PATH . 'assets/js/tabs/index.asset.php' );

		$this->asset_info = [ 
			'tabs'    => $tabs_asset_file,
			'welcome' => $welcome_asset_file,
		];
	}
}
