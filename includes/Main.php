<?php
namespace Sreshto\SwiftCoupons;

/**
 * Main class for the Swift Coupons plugin.
 *
 * This class handles the initialization, licensing, and integration
 * with WooCommerce for the Swift Coupons plugin.
 *
 * @package Sreshto\SwiftCoupons
 * @author Rehan Adil
 */
class Main
{
	// Singleton instance of the class
	public static $instance;

	// Array of coupon meta keys and their types
	private static $coupon_metas = [ 
		'_swiftcou_qualifiers' => 'array',
		'_swiftcou_bxgx'       => 'array',
		'_swiftcou_scheduler'  => 'array',
		'_swiftcou_url_apply'  => 'array',
		'_swiftcou_auto_apply' => 'array',
	];

	/**
	 * Constructor to initialize the class and add necessary actions.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	private function __construct()
	{
		// Add action to check WooCommerce dependency on plugin load
		add_action( 'plugins_loaded', [ $this, 'initialize_plugin' ] );

		// Hook into WooCommerce initialization to declare compatibility with custom order tables
		add_action( 'before_woocommerce_init', [ $this, 'declare_woo_compatibility' ] );

		// Hook into the 'plugins_loaded' action to load the plugin's text domain for translations
		add_action( 'plugins_loaded', [ $this, 'load_text_domain' ] );
	}

	/**
	 * Initializes the plugin by adding actions and filters.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function initialize_plugin()
	{
		add_action( 'admin_menu', [ $this, 'active_admin_menu' ], 99999 );
		add_action( 'woocommerce_coupon_data_panels', [ $this, 'add_coupon_data_panel' ], 10, 2 );
		add_action( 'save_post', [ $this, 'save_coupon_data' ] );
		add_filter( 'woocommerce_coupon_data_tabs', [ $this, 'add_coupon_data_tab' ] );

		// Initialize plugin instances
		self::initialize_instances();

		// Register activation hook for the plugin
		register_activation_hook( SWIFT_COUPON_BASE_FILE, [ $this, 'on_plugin_activation' ] );

		// Add admin initialization action
		add_action( 'admin_init', [ $this, 'on_admin_init' ] );
	}

	/**
	 * Displays an admin notice if WooCommerce is not active.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function woocommerce_missing_notice()
	{
		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Swift Coupons requires WooCommerce to be installed and activated. Please install and activate WooCommerce.', 'swift-coupons' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Checks if WooCommerce is active.
	 *
	 * @author Rehan Adil
	 * @return bool True if WooCommerce is active, false otherwise.
	 */
	private function is_woocommerce_active()
	{
		// Include plugin.php for plugin-related functions
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Check if WooCommerce class exists and plugin is active
		return class_exists( 'WooCommerce' ) && is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @author Rehan Adil
	 * @return Main The singleton instance.
	 */
	public static function instance()
	{
		// Check if instance is null
		if ( self::$instance === null )
			// Create a new instance
			self::$instance = new self();

		// Return the singleton instance
		return self::$instance;
	}

	/**
	 * Adds the admin menu for the active state of the plugin.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function active_admin_menu()
	{
		// Add main menu page for Swift Coupons
		add_menu_page(
			'Swift Coupons',
			'Swift Coupons',
			'activate_plugins',
			'swift-coupons',
			[ $this, 'license_root' ],
			'dashicons-sreshto-swift-coupons',
			'58.5',
		);

		// Add submenu page for home
		add_submenu_page(
			'swift-coupons',
			'Swift Coupons - Welcome',
			'Welcome',
			'activate_plugins',
			'swift-coupons',
			[ $this, 'license_root' ],
		);

		// Add submenu page for Coupons
		add_submenu_page(
			'swift-coupons',
			'Swift Coupons',
			'Coupons',
			'activate_plugins',
			'edit.php?post_type=shop_coupon',
			'',
		);
	}

	/**
	 * Adds the admin menu for the inactive state of the plugin.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function inactive_menu()
	{
		// Add main menu page for Swift Coupons
		add_menu_page(
			'Swift Coupons',
			'Swift Coupons',
			'activate_plugins',
			'swift-coupons',
			[ $this, 'license_root' ],
			'dashicons-tickets-alt',
			'58.5',
		);

		// Add submenu page for home
		add_submenu_page(
			'swift-coupons',
			'Swift Coupons - Licensing',
			'Activate License',
			'activate_plugins',
			'swift-coupons',
			[ $this, 'license_root' ],
		);

		// Add submenu page for licensing
		add_submenu_page(
			'swift-coupons',
			'Swift Coupons',
			'Coupons',
			'activate_plugins',
			'edit.php?post_type=shop_coupon',
			'',
		);
	}

	/**
	 * Displays the root element for the license application.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function license_root()
	{
		echo '<div id="swift-coupons-license-root"></div>';
	}

	/**
	 * Initializes instances of various components.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	private function initialize_instances()
	{
		// Initialize Scripts instance
		Scripts::instance();

		// Initialize Rest_API instance
		Rest_API::instance();

		// Initialize Qualifier instance
		Qualifier::instance();

		// Initialize BXGX instance
		BXGX::instance();

		// Initialize Scheduler instance
		Scheduler::instance();

		// Initialize URL_Coupons instance
		URL_Coupons::instance();
	}

	/**
	 * Adds custom tabs to the WooCommerce coupon data panel.
	 *
	 * @author Rehan Adil
	 * @param array $tabs Existing tabs.
	 * @return array Modified tabs.
	 */
	public function add_coupon_data_tab( $tabs )
	{
		// Merge existing tabs with custom tabs
		return array_merge( $tabs, array(
			'swiftcou_qualifiers' => array(
				'label'  => __( 'Cart Qualifiers', 'swift-coupons' ),
				'target' => 'swiftcou_qualifiers_root',
				'class'  => '',
			),

			'swiftcou_bxgx_deals' => array(
				'label'  => __( 'BXGX Deals', 'swift-coupons' ),
				'target' => 'swiftcou_bxgx_deals_root',
				'class'  => '',
			),

			'swiftcou_scheduler'  => array(
				'label'  => __( 'Scheduler', 'swift-coupons' ),
				'target' => 'swiftcou_scheduler_root',
				'class'  => '',
			),

			'swiftcou_url_apply'  => array(
				'label'  => __( 'URL Coupons', 'swift-coupons' ),
				'target' => 'swiftcou_url_apply_root',
				'class'  => '',
			),

			'swiftcou_auto_apply' => array(
				'label'  => __( 'Auto Apply', 'swift-coupons' ),
				'target' => 'swiftcou_auto_apply_root',
				'class'  => '',
			),
		) );
	}

	/**
	 * Adds custom panels to the WooCommerce coupon data panel.
	 *
	 * @author Rehan Adil
	 * @param int   $coupon_id Coupon ID.
	 * @param mixed $coupon    Coupon object.
	 * @return void
	 */
	public function add_coupon_data_panel( $coupon_id, $coupon )
	{
		?>
		<div id="swiftcou_qualifiers_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcou_bxgx_deals_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcou_scheduler_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcou_url_apply_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcou_auto_apply_root" class="panel woocommerce_options_panel"></div>
		<?php
	}

	/**
	 * Saves custom coupon data when a coupon is saved.
	 *
	 * @author Rehan Adil
	 * @param int $post_id Post ID of the coupon.
	 * @return void
	 */
	public function save_coupon_data( $post_id )
	{
		// Check if post type is 'shop_coupon'
		if ( 'shop_coupon' !== get_post_type( $post_id ) )
			return;

		// Create a new WC_Coupon object
		$coupon = new \WC_Coupon( $post_id );

		// Get allowed coupon meta keys
		$allowed_keys = self::$coupon_metas;

		// Iterate through allowed keys
		foreach ( $allowed_keys as $key => $type )
		{
			// Check if key exists in POST data
			if ( array_key_exists( $key, $_POST ) )
			{
				// Get and decode the data
				$data = $_POST[ $key ];
				$data = json_decode( stripslashes( $data ), true );
				$data = Utilities::sanitize_array( $data );

				// Handle specific keys
				switch ( $key )
				{
					case '_swiftcou_qualifiers':
						// Process qualifiers data
						if ( isset( $data[ 'data' ] ) )
						{
							foreach ( $data[ 'data' ] as $group_index => $group )
							{
								if ( empty( $group[ 'rules' ] ) )
									unset( $data[ 'data' ][ $group_index ] );
							}

							$data[ 'data' ] = array_values( $data[ 'data' ] );
						}
						break;
				}

				// Update post meta with sanitized data
				update_post_meta( $post_id, $key, $data );

				// Handle specific keys for additional actions
				switch ( $key )
				{
					case '_swiftcou_auto_apply':
						// Process auto-apply data
						$is_enabled = isset( $data[ 'enabled' ] ) && boolval( $data[ 'enabled' ] );
						$allow_user_to_remove = isset( $data[ 'allow_user_to_remove' ] ) && boolval( $data[ 'allow_user_to_remove' ] );

						$auto_apply_coupons_option = get_option( '_swiftcou_auto_apply_coupons', [] );
						$exists_in_option = array_key_exists( $coupon->get_code(), $auto_apply_coupons_option );

						if ( ! $is_enabled )
						{
							if ( $exists_in_option )
								unset( $auto_apply_coupons_option[ $coupon->get_code()] );
						}
						else
							$auto_apply_coupons_option[ $coupon->get_code()] = $allow_user_to_remove;

						$auto_apply_coupons_option = Utilities::sanitize_array( $auto_apply_coupons_option );

						update_option( '_swiftcou_auto_apply_coupons', $auto_apply_coupons_option );
						break;

					case '_swiftcou_url_apply':
						// Process URL apply data
						$code_override = isset( $data[ 'enabled' ], $data[ 'code_override' ] ) && ! empty( $data[ 'code_override' ] ) && $data[ 'enabled' ] == true ? sanitize_text_field( $data[ 'code_override' ] ) : '';

						update_post_meta(
							$post_id,
							'_swiftcou_url_apply_override_code',
							$code_override,
						);
						break;
				}
			}
		}
	}

	/**
	 * Handles actions to perform when the plugin is activated.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function on_plugin_activation()
	{
		// Add activation redirect option
		add_option( 'swift_coupons_activation_redirect', true );

		// Add rewrite rule for coupon application
		add_rewrite_rule( '^coupon/([^/]*)/?', 'index.php?swiftcou_apply_coupon_code=$matches[1]', 'top' );

		// Add rewrite tag for coupon code
		add_rewrite_tag( '%swiftcou_apply_coupon_code%', '([^&]+)' );

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Handles admin initialization tasks.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function on_admin_init()
	{
		// Check if activation redirect option is set
		if ( get_option( 'swift_coupons_activation_redirect', false ) )
		{
			// Delete activation redirect option
			delete_option( 'swift_coupons_activation_redirect' );

			// Redirect to Swift Coupons admin page if not multi-activation
			if ( ! isset( $_GET[ 'activate-multi' ] ) )
			{
				wp_safe_redirect( admin_url( 'admin.php?page=swift-coupons' ) );
				exit;
			}
		}
	}

	public function declare_woo_compatibility()
	{
		// Check if the WooCommerce FeaturesUtil class exists
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class) )
		{
			// Declare compatibility with WooCommerce custom order tables
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				SWIFT_COUPON_BASE_FILE,
				true,
			);
		}
	}

	public function load_text_domain()
	{
		// Load the plugin's text domain for localization
		load_plugin_textdomain(
			'swift-coupons', // Text domain for the plugin
			false,           // Deprecated argument, always set to false
			dirname( plugin_basename( SWIFT_COUPON_BASE_FILE ) ) . '/languages' // Path to the languages directory
		);
	}
}
