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
			'swiftcou-tabs',
			SWIFT_COUPON_BASE_URL . '/assets/js/tabs/index.js',
			$this->asset_info[ 'tabs' ][ 'dependencies' ],
			$this->asset_info[ 'tabs' ][ 'version' ],
			true,
		);
		wp_register_script(
			'swiftcou-welcome',
			SWIFT_COUPON_BASE_URL . '/assets/js/welcome/index.js',
			$this->asset_info[ 'welcome' ][ 'dependencies' ],
			$this->asset_info[ 'welcome' ][ 'version' ],
			true,
		);

		// Register styles
		wp_register_style(
			'swiftcou-main',
			SWIFT_COUPON_BASE_URL . '/assets/css/style.css',
			[],
			filemtime( SWIFT_COUPON_BASE_PATH . '/assets/css/style.css' ),
		);
		wp_register_style(
			'swiftcou-custom',
			SWIFT_COUPON_BASE_URL . '/assets/css/custom.css',
			[],
			filemtime( SWIFT_COUPON_BASE_PATH . '/assets/css/custom.css' ),
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

		wp_enqueue_style( 'swiftcou-main' );
		wp_enqueue_style( 'swiftcou-custom' );
		wp_set_script_translations(
			'swiftcou-tabs',
			'swift-coupons',
			plugin_dir_path( SWIFT_COUPON_BASE_FILE ) . 'languages'
		);

		if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'swift-coupons' )
		{
			wp_enqueue_script( 'swiftcou-welcome' );
		}

		// Coupon-related admin pages
		$is_coupon_page = (
			( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'shop_coupon' ) ||
			( isset( $_GET[ 'post' ] ) && get_post_type( $_GET[ 'post' ] ) === 'shop_coupon' )
		);

		if ( $is_coupon_page )
		{

			$coupon_id = isset( $_GET[ 'post' ] ) ? absint( $_GET[ 'post' ] ) : 0;
			$coupon    = $coupon_id > 0 ? new \WC_Coupon( $coupon_id ) : null;

			$coupon_code          = $coupon ? $coupon->get_code() : '';
			$coupon_discount_type = $coupon ? $coupon->get_discount_type() : '';
			$coupon_id            = $coupon ? $coupon->get_id() : 0;

			$single_coupon_data = $coupon ? [ 
				'qualifiers' => get_post_meta( $coupon_id, '_swiftcou_qualifiers', true ) ?: [],
				'bxgx'       => get_post_meta( $coupon_id, '_swiftcou_bxgx', true ) ?: [],
				'scheduler'  => get_post_meta( $coupon_id, '_swiftcou_scheduler', true ) ?: [],
				'url_apply'  => get_post_meta( $coupon_id, '_swiftcou_url_apply', true ) ?: [],
				'auto_apply' => get_post_meta( $coupon_id, '_swiftcou_auto_apply', true ) ?: [],
			] : [];

			wp_enqueue_script( 'swiftcou-tabs' );

			wp_localize_script( 'swiftcou-tabs', 'swiftCP', [ 
				'siteUrl'   => site_url(),
				'isPremium' => apply_filters( '_swiftcp_is_premium', false ),
			] );

			wp_localize_script( 'swiftcou-tabs', 'swiftCouponSingle', [ 
				'coupon'    => [ 
					'code' => $coupon_code,
					'id'   => absint( $coupon_id ),
					'type' => $coupon_discount_type,
				],
				'data'      => $single_coupon_data,
				'settings'  => [ 
					'qualifiers' => [ 
						'rules' => apply_filters( 'swiftcou_rules', [] ),
					],
				],
				'pluginUrl' => SWIFT_COUPON_BASE_URL,
			] );
		}
	}

	private function get_asset_info()
	{
		$tabs_asset_file    = include( SWIFT_COUPON_BASE_PATH . 'assets/js/tabs/index.asset.php' );
		$welcome_asset_file = include( SWIFT_COUPON_BASE_PATH . 'assets/js/tabs/index.asset.php' );

		$this->asset_info = [ 
			'tabs'    => $tabs_asset_file,
			'welcome' => $welcome_asset_file,
		];
	}
}
