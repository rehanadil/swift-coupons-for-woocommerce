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
		'swiftcoupons_qualifiers' => 'array',
		'swiftcoupons_bxgx'       => 'array',
		'swiftcoupons_scheduler'  => 'array',
		'swiftcoupons_url_apply'  => 'array',
		'swiftcoupons_auto_apply' => 'array',
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

		// Register activation hook for the plugin
		register_activation_hook( SWIFT_COUPONS_BASE_FILE, [ $this, 'on_plugin_activation' ] );

		// Register deactivation hook for the plugin
		register_deactivation_hook( SWIFT_COUPONS_BASE_FILE, [ $this, 'on_plugin_deactivation' ] );
	}

	/**
	 * Initializes the plugin by adding actions and filters.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function initialize_plugin()
	{
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 99999 );
		add_action( 'woocommerce_coupon_data_panels', [ $this, 'add_coupon_data_panel' ], 10, 2 );
		add_action( 'save_post', [ $this, 'save_coupon_data' ] );
		add_filter( 'woocommerce_coupon_data_tabs', [ $this, 'add_coupon_data_tab' ] );

		// Initialize plugin instances
		self::initialize_instances();

		// Add admin initialization action
		add_action( 'admin_init', [ $this, 'on_admin_init' ] );
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
	public function admin_menu()
	{
		// Add main menu page for Swift Coupons
		add_menu_page(
			__( 'Swift Coupons', 'swift-coupons-for-woocommerce' ),
			__( 'Swift Coupons', 'swift-coupons-for-woocommerce' ),
			'activate_plugins',
			'swift-coupons',
			[ $this, 'welcome_root' ],
			'dashicons-sreshto-swift-coupons',
			'58.5',
		);

		// Add submenu page for home
		add_submenu_page(
			'swift-coupons',
			__( 'Swift Coupons - Welcome', 'swift-coupons-for-woocommerce' ),
			__( 'Welcome', 'swift-coupons-for-woocommerce' ),
			'activate_plugins',
			'swift-coupons',
			[ $this, 'welcome_root' ],
		);

		// Add submenu page for Coupons
		add_submenu_page(
			'swift-coupons',
			__( 'Manage Swift Coupons', 'swift-coupons-for-woocommerce' ),
			__( 'Manage Coupons', 'swift-coupons-for-woocommerce' ),
			'activate_plugins',
			'edit.php?post_type=shop_coupon',
			'',
			2,
		);

		do_action( 'swift-coupons/admin-menu' );
	}

	/**
	 * Displays the root element for the license application.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function welcome_root()
	{
		echo '<div id="swift-coupons-welcome-root"></div>';
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
			'swiftcoupons_qualifiers' => array(
				'label'  => __( 'Cart Qualifiers', 'swift-coupons-for-woocommerce' ),
				'target' => 'swiftcoupons_qualifiers_root',
				'class'  => '',
			),

			'swiftcoupons_bxgx_deals' => array(
				'label'  => __( 'BXGX Deals', 'swift-coupons-for-woocommerce' ),
				'target' => 'swiftcoupons_bxgx_deals_root',
				'class'  => '',
			),

			'swiftcoupons_scheduler'  => array(
				'label'  => __( 'Scheduler', 'swift-coupons-for-woocommerce' ),
				'target' => 'swiftcoupons_scheduler_root',
				'class'  => '',
			),

			'swiftcoupons_url_apply'  => array(
				'label'  => __( 'URL Coupons', 'swift-coupons-for-woocommerce' ),
				'target' => 'swiftcoupons_url_apply_root',
				'class'  => '',
			),

			'swiftcoupons_auto_apply' => array(
				'label'  => __( 'Auto Apply', 'swift-coupons-for-woocommerce' ),
				'target' => 'swiftcoupons_auto_apply_root',
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
		<div id="swiftcoupons_qualifiers_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcoupons_bxgx_deals_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcoupons_scheduler_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcoupons_url_apply_root" class="panel woocommerce_options_panel"></div>
		<div id="swiftcoupons_auto_apply_root" class="panel woocommerce_options_panel"></div>
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
		// Only run on coupon post type and in admin
		if ( 'shop_coupon' !== get_post_type( $post_id ) || ! is_admin() )
			return;

		// Only save on Publish/Update (not autosave, ajax, or bulk)
		if (
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			( defined( 'DOING_AJAX' ) && DOING_AJAX ) ||
			( defined( 'DOING_CRON' ) && DOING_CRON )
		)
			return;

		$nonce = isset( $_POST[ '_wpnonce' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_wpnonce' ] ) ) : '';

		if ( ! isset( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $nonce, "update-post_{$post_id}" ) )
			return;

		// Check user capability
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Only save if Publish/Update button is clicked
		if (
			isset( $_POST[ 'save' ] ) || // Save Draft
			isset( $_POST[ 'publish' ] ) || // Publish
			isset( $_POST[ 'update' ] ) // Update
		)
		{
			$coupon       = new \WC_Coupon( $post_id );
			$allowed_keys = self::$coupon_metas;

			foreach ( $allowed_keys as $key => $type )
			{
				if ( array_key_exists( $key, $_POST ) )
				{
					$post_key = sanitize_text_field( wp_unslash( $key ) );
					$data     = isset( $_POST[ $post_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) ) : '';
					$data     = json_decode( stripslashes( $data ), true );
					$data     = Utilities::sanitize_array( $data );

					switch ( $key )
					{
						case 'swiftcoupons_qualifiers':
							if ( isset( $data[ 'data' ] ) )
							{
								foreach ( $data[ 'data' ] as $group_index => $group )
								{
									if ( empty( $group[ 'rules' ] ) )
									{
										unset( $data[ 'data' ][ $group_index ] );
									}
								}
								$data[ 'data' ] = array_values( $data[ 'data' ] );
							}
							break;
					}

					update_post_meta( $post_id, $key, $data );

					switch ( $key )
					{
						case 'swiftcoupons_auto_apply':
							$is_enabled = isset( $data[ 'enabled' ] ) && boolval( $data[ 'enabled' ] );
							$allow_user_to_remove = isset( $data[ 'allow_user_to_remove' ] ) && boolval( $data[ 'allow_user_to_remove' ] );

							$auto_apply_coupons_option = Utilities::esc_array( get_option( 'swiftcoupons_auto_apply_coupons', [] ) );
							$exists_in_option = array_key_exists( $coupon->get_code(), $auto_apply_coupons_option );

							if ( ! $is_enabled )
							{
								if ( $exists_in_option )
								{
									unset( $auto_apply_coupons_option[ $coupon->get_code()] );
								}
							}
							else
							{
								$auto_apply_coupons_option[ $coupon->get_code()] = $allow_user_to_remove;
							}

							$auto_apply_coupons_option = Utilities::sanitize_array( $auto_apply_coupons_option );
							update_option( 'swiftcoupons_auto_apply_coupons', $auto_apply_coupons_option );
							break;

						case 'swiftcoupons_url_apply':
							$code_override = isset( $data[ 'enabled' ], $data[ 'code_override' ] ) && ! empty( $data[ 'code_override' ] ) && $data[ 'enabled' ] == true ? sanitize_text_field( $data[ 'code_override' ] ) : '';
							update_post_meta(
								$post_id,
								'swiftcoupons_url_apply_override_code',
								$code_override,
							);
							break;
					}
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
		// Add option to track plugin activation time
		add_option( 'swift_coupons_activate_time', time() );

		// Add activation redirect option
		add_option( 'swift_coupons_activation_redirect', true );

		// Add rewrite rule for coupon application
		add_rewrite_rule( '^coupon/([^/]*)/?', 'index.php?swiftcoupons_apply_coupon_code=$matches[1]', 'top' );

		// Add rewrite tag for coupon code
		add_rewrite_tag( '%swiftcoupons_apply_coupon_code%', '([^&]+)' );

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Handles actions to perform when the plugin is deactivated.
	 *
	 * @author Rehan Adil
	 * @return void
	 */
	public function on_plugin_deactivation()
	{
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
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
				SWIFT_COUPONS_BASE_FILE,
				true,
			);
		}
	}
}
