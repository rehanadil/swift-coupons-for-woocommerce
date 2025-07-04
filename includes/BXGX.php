<?php
namespace Sreshto\SwiftCoupons;

/**
 * Class BXGX
 * Handles Buy X Get X (BXGX) coupon logic for WooCommerce.
 * 
 * @package Sreshto\SwiftCoupons
 * @author Rehan Adil
 */
class BXGX
{
	// Stores BXGX data for coupons
	private array $data;

	// Singleton instance of the class
	public static $instance;

	/**
	 * BXGX constructor.
	 * Registers WooCommerce hooks for applying and managing BXGX deals.
	 * 
	 * @author Rehan Adil
	 */
	private function __construct()
	{
		// Hook to apply BXGX deals when a coupon is applied
		add_action( 'woocommerce_applied_coupon', [ $this, 'apply_bxgx_deals' ] );

		// Hook to update cart item prices before calculating totals
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'update_cart_item_prices' ], 10, 1 );

		// Hook to recheck BXGX deals after cart item quantity is updated
		add_action( 'woocommerce_after_cart_item_quantity_update', [ $this, 'recheck_bxgx_deals' ], 10, 2 );

		// Hook to clear BXGX deals when a coupon is removed
		add_action( 'woocommerce_removed_coupon', [ $this, 'clear_deals_on_coupon_removal' ], 10, 1 );
	}

	/**
	 * Singleton instance method.
	 * Ensures only one instance of the class is created.
	 * 
	 * @return BXGX
	 * @author Rehan Adil
	 */
	public static function instance()
	{
		// Check if the instance is null
		if ( is_null( self::$instance ) )
		{
			// Create a new instance
			self::$instance = new self();
		}

		// Return the instance
		return self::$instance;
	}

	/**
	 * Apply BXGX deals for applied coupons.
	 * 
	 * @author Rehan Adil
	 */
	public function apply_bxgx_deals()
	{
		// Ensure the function is not called in the admin area and the user is logged in
		if ( ! is_admin() && is_user_logged_in() )
		{
			// Get all applied coupon codes
			$coupon_codes = WC()->cart->get_applied_coupons();

			// Iterate through each coupon code
			foreach ( $coupon_codes as $code )
			{
				// Create a WooCommerce coupon object
				$coupon = new \WC_Coupon( $code );

				// Check if the BXGX deal can be applied
				if ( $this->can_apply_bxgx_deal( $coupon ) )
				{
					// Retrieve BXGX data for the coupon
					$data = $this->get_bxgx_data( $coupon );

					// Process the BXGX deal
					$this->process_bxgx_deal( $coupon, $data );
				}
			}
		}
	}

	/**
	 * Check if a BXGX deal can be applied for a given coupon.
	 * 
	 * @param \WC_Coupon $coupon WooCommerce coupon object.
	 * @return bool True if the deal can be applied, false otherwise.
	 * @author Rehan Adil
	 */
	private function can_apply_bxgx_deal( $coupon )
	{
		// Retrieve BXGX data for the coupon
		$data = $this->get_bxgx_data( $coupon );

		// Check if the deal is enabled
		return $data && isset( $data[ 'enabled' ] ) && boolval( $data[ 'enabled' ] ) ? true : false;
	}

	/**
	 * Retrieve BXGX data for a given coupon.
	 * 
	 * @param \WC_Coupon $coupon WooCommerce coupon object.
	 * @return array|null BXGX data or null if not found.
	 * @author Rehan Adil
	 */
	private function get_bxgx_data( $coupon )
	{
		// Get the coupon ID
		$coupon_id = $coupon->get_id();

		// Check if the data is already cached
		if ( isset( $this->data[ $coupon_id ] ) )
			return $this->data[ $coupon_id ];

		// Retrieve BXGX data from post meta
		$data = get_post_meta( $coupon_id, 'swiftcoupons_bxgx', true );

		// Validate the data
		$valid = $data && is_array( $data );

		// Cache and return the data if valid
		if ( $valid )
		{
			$this->data[ $coupon_id ] = $data;
			return $data;
		}

		// Return null if data is invalid
		return null;
	}

	/**
	 * Process a BXGX deal for a given coupon and data.
	 * 
	 * @param \WC_Coupon $coupon WooCommerce coupon object.
	 * @param array $data BXGX data.
	 * @author Rehan Adil
	 */
	private function process_bxgx_deal( $coupon, $data )
	{
		// Retrieve "buy" and "get" items from the data
		$buy_items = $data[ 'buy' ][ 'items' ];
		$get_items = $data[ 'get' ][ 'items' ];

		// Check if the "buy" items match the conditions
		if ( $this->match_buy_items( $buy_items, $data[ 'buy' ][ 'match' ] ) )
		{
			// Apply the "get" items
			$this->apply_get_items( $coupon, $get_items, $data[ 'get' ][ 'apply' ] );
		}
	}

	/**
	 * Match "buy" items in the cart against the BXGX conditions.
	 * 
	 * @param array $items List of "buy" items.
	 * @param string $match_type
	 * @return bool True if the conditions are met, false otherwise.
	 * @author Rehan Adil
	 */
	private function match_buy_items( $items, $match_type )
	{
		// Initialize matched count
		$matched = 0;

		// Iterate through each "buy" item
		foreach ( $items as $item )
		{
			switch ( $item[ 'type' ] )
			{
				case 'product':
					$matched += $this->match_product( $item );
					break;

				default:
					$matched += apply_filters( 'swift-coupons/bxgx-match-item', 0, $item, $match_type, $items, $matched );
					break;
			}
		}

		// Return true if all items are matched
		return apply_filters( 'swift-coupons/bxgx-match-items-final-bool', $matched === count( $items ), $matched, $match_type, $items );
	}

	/**
	 * Match a product in the cart against BXGX conditions.
	 * 
	 * @param array $item Product item data.
	 * @return bool True if the product matches the conditions, false otherwise.
	 * @author Rehan Adil
	 */
	private function match_product( $item )
	{
		// Iterate through each cart item
		foreach ( WC()->cart->get_cart() as $cart_item )
		{
			// Check if the product ID and quantity match the conditions
			if ( $cart_item[ 'product_id' ] == $item[ 'id' ] && $cart_item[ 'quantity' ] >= $item[ 'quantity' ][ 'value' ] )
				return true;
		}

		// Return false if no match is found
		return false;
	}

	/**
	 * Apply "get" items in the cart based on BXGX conditions.
	 * 
	 * @param \WC_Coupon $coupon WooCommerce coupon object.
	 * @param array $items List of "get" items.
	 * @param string $apply_type Apply type
	 * @author Rehan Adil
	 */
	private function apply_get_items( $coupon, $items, $apply_type )
	{
		switch ( $apply_type )
		{
			case 'all':
				// Iterate through each "get" item
				foreach ( $items as $item )
					$this->apply_discount( $coupon, $item );
				break;

			default:
				do_action( 'swift-coupons/bxgx-apply-get-items', $apply_type, $items, $coupon, $this );
				break;
		}
	}

	/**
	 * Apply discount for a "get" item in the cart.
	 * 
	 * @param \WC_Coupon $coupon WooCommerce coupon object.
	 * @param array $item "Get" item data.
	 * @author Rehan Adil
	 */
	public function apply_discount( $coupon, $item )
	{
		switch ( $item[ 'type' ] )
		{
			case 'product':
				// Apply discount for the product
				$this->apply_item_discount( $coupon, $item );
				break;

			default:
				do_action( 'swift-coupons/bxgx-apply-discount', $item, $coupon, $this );
				break;
		}
	}

	/**
	 * Apply discount for a specific product item in the cart.
	 * 
	 * @param \WC_Coupon $coupon WooCommerce coupon object.
	 * @param array $item Product item data.
	 * @param string|null $cart_item_key Cart item key (optional).
	 * @author Rehan Adil
	 */
	public function apply_item_discount( $coupon, $item, $cart_item_key = null )
	{
		// Get product ID, quantity, and discount data
		$product_id = $item[ 'id' ];
		$quantity   = $item[ 'quantity' ][ 'value' ];
		$discount   = $item[ 'discount' ];
		$product    = wc_get_product( $product_id );

		// Calculate deal price based on discount type
		if ( $discount[ 'type' ] === 'percent' )
		{
			$deal_price = floatval( $product->get_price() ) * ( 1 - floatval( $discount[ 'value' ] ) / 100 );
		}
		elseif ( $discount[ 'type' ] === 'fixed' )
		{
			$deal_price = floatval( $product->get_price() ) - floatval( $discount[ 'value' ] );
		}
		elseif ( $discount[ 'type' ] === 'override_price' )
		{
			$deal_price = floatval( $discount[ 'value' ] );
		}

		// Add the product to the cart with the deal price and BXGX metadata
		WC()->cart->add_to_cart( $product_id, $quantity, 0, [], [ 
			'swiftcoupons_bxgx_price'        => $deal_price,
			'swiftcoupons_bxgx_min_quantity' => $quantity,
			'swiftcoupons_bxgx_coupon_code'  => $coupon->get_code(),
		] );
	}

	/**
	 * Update the deal price for a cart item.
	 * 
	 * @param float $discount Discount amount.
	 * @param float $discounting_amount Discounting amount.
	 * @param array $cart_item Cart item data.
	 * @param bool $single Whether the discount is for a single item.
	 * @param \WC_Coupon $coupon WooCommerce coupon object.
	 * @return float Updated discount amount.
	 * @author Rehan Adil
	 */
	public function update_item_deal_price( $discount, $discounting_amount, $cart_item, $single, $coupon )
	{
		// Check if the cart item is part of a BXGX deal
		if ( self::is_cart_item_a_deal( $cart_item ) )
		{
			// Calculate the discount based on the deal price and quantity
			$deal_price = $cart_item[ 'swiftcoupons_bxgx_price' ];
			$discount   = $cart_item[ 'swiftcoupons_bxgx_min_quantity' ] * ( $cart_item[ 'data' ]->get_price() - $deal_price );
		}

		// Return the updated discount amount
		return $discount;
	}

	/**
	 * Update cart item prices based on BXGX deals.
	 * 
	 * @param \WC_Cart $cart WooCommerce cart object.
	 * @author Rehan Adil
	 */
	public function update_cart_item_prices( $cart )
	{
		// Ensure cart is not empty and is not being calculated for AJAX fragments
		if ( is_admin() && ! defined( 'DOING_AJAX' ) || ! $cart )
			return;

		// Iterate through each cart item
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item )
		{
			// Check if the cart item is part of a BXGX deal
			if ( self::is_cart_item_a_deal( $cart_item ) )
			{
				// Get deal price and quantities
				$deal_price    = $cart_item[ 'swiftcoupons_bxgx_price' ];
				$deal_quantity = $cart_item[ 'swiftcoupons_bxgx_min_quantity' ];
				$cart_quantity = $cart_item[ 'quantity' ];

				// Update the product price in the cart if the quantity is less than or equal to the deal quantity
				if ( $cart_quantity <= $deal_quantity )
					$cart_item[ 'data' ]->set_price( $deal_price );
			}
		}
	}

	/**
	 * Check if a cart item is part of a BXGX deal.
	 * 
	 * @param array $cart_item Cart item data.
	 * @return bool True if the cart item is part of a BXGX deal, false otherwise.
	 * @author Rehan Adil
	 */
	private function is_cart_item_a_deal( $cart_item )
	{
		// Check if the cart item has BXGX metadata and matches the conditions
		return isset( $cart_item[ 'swiftcoupons_bxgx_price' ], $cart_item[ 'swiftcoupons_bxgx_min_quantity' ], $cart_item[ 'swiftcoupons_bxgx_coupon_code' ] )
			&& $cart_item[ 'quantity' ] >= $cart_item[ 'swiftcoupons_bxgx_min_quantity' ]
			&& in_array( $cart_item[ 'swiftcoupons_bxgx_coupon_code' ], WC()->cart->get_applied_coupons(), true );
	}

	/**
	 * Recheck BXGX deals after a cart item quantity is updated.
	 * 
	 * @param string $cart_item_key Cart item key.
	 * @param int $quantity Updated quantity.
	 * @author Rehan Adil
	 */
	public function recheck_bxgx_deals( $cart_item_key, $quantity )
	{
		// Get the cart item data
		$cart_item = WC()->cart->get_cart_item( $cart_item_key );

		// Return if the cart item is already part of a BXGX deal
		if ( isset( $cart_item[ 'swiftcoupons_bxgx_price' ] ) )
			return;

		// Reapply BXGX deals by clearing and reprocessing them
		foreach ( WC()->cart->get_applied_coupons() as $code )
		{
			// Create a WooCommerce coupon object
			$coupon = new \WC_Coupon( $code );

			// Check if the BXGX deal can be applied
			if ( $this->can_apply_bxgx_deal( $coupon ) )
			{
				// Retrieve BXGX data for the coupon
				$data = $this->get_bxgx_data( $coupon );

				// Clear existing deals for the coupon
				self::clear_bxgx_deals( $coupon );

				// Reprocess the BXGX deal
				$this->process_bxgx_deal( $coupon, $data );
			}
		}
	}

	/**
	 * Clear BXGX deals for a given coupon.
	 * 
	 * @param \WC_Coupon|string $coupon WooCommerce coupon object or coupon code.
	 * @author Rehan Adil
	 */
	private static function clear_bxgx_deals( $coupon )
	{
		// Get the coupon code
		$code = is_a( $coupon, 'WC_Coupon' ) ? $coupon->get_code() : $coupon;

		// Remove all cart items associated with the BXGX deal for the given coupon
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item )
		{
			if ( isset( $cart_item[ 'swiftcoupons_bxgx_coupon_code' ] ) && $cart_item[ 'swiftcoupons_bxgx_coupon_code' ] === $code )
			{
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}
	}

	/**
	 * Clear BXGX deals when a coupon is removed.
	 * 
	 * @param string $coupon_code Coupon code.
	 * @author Rehan Adil
	 */
	public function clear_deals_on_coupon_removal( $coupon_code )
	{
		// Clear existing deals for the coupon
		self::clear_bxgx_deals( $coupon_code );
	}
}
