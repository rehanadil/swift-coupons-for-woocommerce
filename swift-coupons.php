<?php
/**
 * Plugin Name: Swift Coupons
 * Plugin URI: https://sreshto.com
 * Description: Swift Coupons is a powerful and feature-rich coupon plugin for WooCommerce that allows you to create and manage coupons for your store.
 * Version: 1.0.0
 * Author: Sreshto
 * Author URI: https://sreshto.com
 * Requires Plugins: woocommerce
 * Requires at least: 6.0
 * Tested up to: 6.8.1
 * WC requires at least: 8.0.0
 * WC tested up to:      9.3.3
 * 
 * Text Domain: swift-coupons
 * Domain Path: /languages/
 *
 * @package SwiftCoupon
 * @author Rehan Adil
 */

// Exit if accessed directly to ensure security
if ( ! defined( 'ABSPATH' ) )
	exit;

// Define the plugin version
const SWIFT_COUPON_VERSION = '1.0.0';

// Define the base file path of the plugin
const SWIFT_COUPON_BASE_FILE = __FILE__;

// Define the base directory path of the plugin
define( 'SWIFT_COUPON_BASE_PATH', plugin_dir_path( SWIFT_COUPON_BASE_FILE ) );

// Define the base URL of the plugin
define( 'SWIFT_COUPON_BASE_URL', plugins_url( '', SWIFT_COUPON_BASE_FILE ) );

// Include the Composer autoloader
require_once SWIFT_COUPON_BASE_PATH . '/vendor/autoload.php';

// Initialize the main plugin class
\Sreshto\SwiftCoupon\Main::instance();
