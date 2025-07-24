<?php
/**
 * Plugin Name: Swift Coupons for WooCommerce
 * Plugin URI: https://swiftcoupons.com
 * Description: Swift Coupons for WooCommerce is a powerful and feature-rich coupon plugin that allows you to create and manage coupons for your store.
 * Version: 1.0.2
 * Author: Sreshto
 * Author URI: https://sreshto.com
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 6.3
 * Tested up to: 6.8.1
 * Requires Plugins: woocommerce
 * WC requires at least: 8.0.0
 * WC tested up to:      9.9.4
 * 
 * Text Domain: swift-coupons-for-woocommerce
 * Domain Path: /languages/
 *
 * @package SwiftCoupons
 * @author Rehan Adil
 */

// Exit if accessed directly to ensure security
if ( ! defined( 'ABSPATH' ) )
	exit;

// Define the plugin version
const SWIFT_COUPONS_VERSION = '1.0.2';

// Define the base file path of the plugin
const SWIFT_COUPONS_BASE_FILE = __FILE__;

// Define the base directory path of the plugin
define( 'SWIFT_COUPONS_BASE_PATH', plugin_dir_path( SWIFT_COUPONS_BASE_FILE ) );

// Define the base URL of the plugin
define( 'SWIFT_COUPONS_BASE_URL', plugins_url( '', SWIFT_COUPONS_BASE_FILE ) );

// Include the Composer autoloader
require_once SWIFT_COUPONS_BASE_PATH . '/vendor/autoload.php';

// Initialize the main plugin class
\Sreshto\SwiftCoupons\Main::instance();
