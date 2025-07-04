<?php
// Define the namespace for the class.
namespace Sreshto\SwiftCoupons;

// Import necessary WordPress classes and the Qualifier class.
use \WP_REST_Server, \WP_REST_Controller, \WP_REST_Request, \WP_REST_Response, \Sreshto\SwiftCoupons\Qualifier;

/**
 * Class Rest_API
 * 
 * Handles the registration and implementation of custom REST API routes for the Swift Coupons plugin.
 * 
 * This class provides endpoints for managing coupon qualifiers, searching products and categories,
 * retrieving user roles, and fetching payment methods. It follows the singleton pattern to ensure
 * a single instance is used throughout the application.
 * 
 * @package Sreshto\SwiftCoupons
 * @since 1.0.0
 * @version 1.0.0
 */
class Rest_API extends WP_REST_Controller
{
	// Define a constant for the REST API namespace.
	const NAMESPACE = 'swift-coupons/v1';

	// Declare a static property to hold the singleton instance.
	public static $instance = null;

	/**
	 * Constructor to initialize the class and register REST API routes.
	 * 
	 * @author Rehan Adil
	 */
	private function __construct()
	{
		// Hook the register_routes method to the rest_api_init action.
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Returns the singleton instance of the Rest_API class.
	 * 
	 * @return Rest_API The singleton instance of the class.
	 * @author Rehan Adil
	 */
	public static function instance()
	{
		// Check if the instance is null, and create a new one if necessary.
		if ( is_null( self::$instance ) )
			self::$instance = new self();

		// Return the singleton instance.
		return self::$instance;
	}

	/**
	 * Checks if the current user is an admin.
	 * 
	 * @return bool True if the user is an admin, false otherwise.
	 * @author Rehan Adil
	 */
	public function is_admin()
	{
		// Uncomment the following line to check if the user has the manage_options capability.
		return current_user_can( 'manage_options' );
	}

	/**
	 * Registers all REST API routes for the plugin.
	 * 
	 * @author Rehan Adil
	 */
	public function register_routes()
	{
		// Registers a REST route for searching products by name.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/products/search',
			// The route options.
			array(
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'search_products_by_name' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ),
			),
		);

		// Registers a REST route for searching categories by name.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/categories/search',
			// The route options.
			array(
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'search_categories_by_name' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ),
			),
		);

		// Registers a REST route for searching categories by name.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/coupons/search/code',
			// The route options.
			array(
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'search_coupons_by_code' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ),
			),
		);

		// Registers a REST route for retrieving user roles.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/users/roles',
			// The route options.
			[ 
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'get_users_roles' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ), // Adjust permissions as needed.
			],
		);

		// Registers a REST route for retrieving payment methods.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/payment-methods',
			// The route options.
			[ 
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'get_payment_methods' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ), // Adjust permissions as needed.
			],
		);

		// Registers a REST route for searching products.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/product-search',
			// The route options.
			[ 
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'search_products_by_name' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ), // Adjust permissions as needed.
			],
		);

		// Registers a REST route for searching categories.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/category-search',
			// The route options.
			[ 
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'search_categories_by_name' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ), // Adjust permissions as needed.
			],
		);

		// Registers a REST route for searching categories.
		register_rest_route(
				// The namespace.
			self::NAMESPACE ,
			// The route.
			'/rating-unlock',
			// The route options.
			[ 
				// GET method is allowed.
				'methods'             => WP_REST_Server::READABLE,
				// The callback function to be called.
				'callback'            => array( $this, 'rating_unlock' ),
				// The permission callback function to check if the user is allowed to access the route.
				'permission_callback' => array( $this, 'is_admin' ), // Adjust permissions as needed.
			],
		);
	}

	/**
	 * Searches for products by name.
	 * 
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response The response object containing the search results.
	 * @author Rehan Adil
	 */
	public function search_products_by_name( $request )
	{
		// Get the search keyword from the request.
		$search_keyword = $request->has_param( 'query' ) ? $request->get_param( 'query' ) : '';

		// Sanitize the search keyword.
		$product_name = sanitize_text_field( $search_keyword );

		// Search for variable products matching the query.
		$variable_products = get_posts( [ 
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $product_name,
			'posts_per_page' => -1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => [ 
				[ 
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => [ 'variable' ],
				],
			],
		] );

		// Search for simple products matching the query.
		$simple_products = get_posts( [ 
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $product_name,
			'posts_per_page' => -1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => [ 
				[ 
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => [ 'simple' ],
				],
			],
		] );

		$results = array();

		// Handle variable products: search their variations.
		foreach ( $variable_products as $parent_product )
		{
			$children = get_posts( array(
				'post_type'      => 'product_variation',
				'post_status'    => 'publish',
				'post_parent'    => $parent_product->ID,
				'posts_per_page' => -1,
				's'              => $product_name,
			) );
			foreach ( $children as $variation )
			{
				$variation_obj = wc_get_product( $variation->ID );
				if ( $variation_obj )
				{
					$label     = $variation_obj->get_formatted_name();
					$results[] = array(
						'value' => $variation->ID,
						'label' => wp_strip_all_tags( $label ),
					);
				}
			}
		}

		// Handle simple products.
		foreach ( $simple_products as $product )
		{
			$results[] = array(
				'value' => $product->ID,
				'label' => wp_strip_all_tags( $product->post_title ),
			);
		}

		$response = [ 
			'success' => true,
			'results' => $results,
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Searches for categories by name.
	 * 
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response The response object containing the search results.
	 * @author Rehan Adil
	 */
	public function search_categories_by_name( $request )
	{
		// Get the search keyword from the request.
		$search_keyword = $request->has_param( 'query' ) ? $request->get_param( 'query' ) : '';

		// Get the category name from the request.
		$category_name = sanitize_text_field( $search_keyword );

		// Get all the categories by name.
		$categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'search'     => $category_name,
		) );

		// Set the results array.
		$results = array();

		// Loop through each product.
		foreach ( $categories as $category )
		{
			// Add the product data to the results array.
			$results[] = array(
				'value' => $category->slug,
				'label' => $category->name,
			);
		}

		$response = [ 
			'success' => true,
			'results' => $results,
		];

		// Return a successful response with the results.
		return rest_ensure_response( $response );
	}

	/**
	 * Searches for coupons by code or ID.
	 * 
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response The response object containing the search results.
	 * @author Rehan Adil
	 */
	public function search_coupons_by_code( $request )
	{
		// Get the search keyword from the request.
		$search_keyword = $request->has_param( 'query' ) ? sanitize_text_field( $request->get_param( 'query' ) ) : '';

		$results = [];

		$args = array(
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			's'              => $search_keyword, // Search by coupon code or ID.
		);

		$coupons = get_posts( $args );

		foreach ( $coupons as $coupon_post )
		{
			$coupon    = new \WC_Coupon( $coupon_post->ID );
			$results[] = array(
				'value' => $coupon->get_code(),
				'label' => $coupon->get_code(),
			);
		}

		$response = [ 
			'success' => true,
			'results' => $results,
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves all user roles.
	 * 
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response The response object containing the user roles.
	 * @author Rehan Adil
	 */
	public function get_users_roles( $request )
	{
		// Access the global WP_Roles object.
		global $wp_roles;

		// Initialize WP_Roles if not already set.
		if ( ! isset( $wp_roles ) )
		{
			$wp_roles = new \WP_Roles();
		}

		// Initialize an array to store user roles.
		$user_roles = [];

		// Loop through each role in WP_Roles.
		foreach ( $wp_roles->roles as $role => $role_data )
		{
			// Add the role data to the user roles array.
			$user_roles[] = array(
				'value' => $role,
				'label' => $role_data[ 'name' ],
			);
		}

		// Prepare the response data.
		$response = [ 
			'success' => true,
			'results' => $user_roles,
		];

		// Return the response.
		return rest_ensure_response( $response );
	}

	/**
	 * Returns a successful response with the provided data.
	 * 
	 * @param mixed $data The data to include in the response.
	 * @return WP_REST_Response The successful response object.
	 * @author Rehan Adil
	 */
	private function success( $data )
	{
		// Create a new WP_REST_Response object with the provided data and a 200 status code.
		return new WP_REST_Response( is_array( $data ) ? $data : array( 'message' => $data ), 200 );
	}

	/**
	 * Returns an error response with the provided data.
	 * 
	 * @param mixed $data The data to include in the response.
	 * @param int $status_code The HTTP status code for the error response.
	 * @return WP_REST_Response The error response object.
	 * @author Rehan Adil
	 */
	private function error( $data, $status_code = 400 )
	{
		// Create a new WP_REST_Response object with the provided data and the specified status code.
		return new WP_REST_Response( is_array( $data ) ? $data : array( 'message' => $data ), $status_code );
	}

	/**
	 * Retrieves all available payment methods.
	 * 
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response The response object containing the payment methods.
	 * @author Rehan Adil
	 */
	public function get_payment_methods( $request )
	{
		// Retrieve all payment gateways from WooCommerce.
		$payment_methods = WC()->payment_gateways->payment_gateways();

		// Initialize an array to store payment methods.
		$methods = [];

		// Loop through each payment gateway.
		foreach ( $payment_methods as $method )
		{
			// Add the payment method data to the methods array.
			$methods[] = array(
				'value' => $method->id,
				'label' => $method->title,
			);
		}

		// Prepare the response data.
		$reponse_data = [ 
			'success' => true,
			'results' => $methods,
		];

		// Create a new WP_REST_Response object with the response data and a 200 status code.
		$response = new WP_REST_Response( $reponse_data, 200 ); // HTTP 200 OK.

		// Return the response.
		return $response;
	}

	public function rating_unlock( $request )
	{
		update_option( 'swift_coupons_rating_unlocked', 'yes' );
		return $this->success( 'Rating unlocked successfully' );
	}
}
