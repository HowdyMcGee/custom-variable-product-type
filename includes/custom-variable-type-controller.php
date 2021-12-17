<?php
/**
 * Custom Variable Product Type Controller
 * Hooks specifically related to custom variable product type.
 */

if( ! defined( 'ABSPATH' ) ) {
	return;
}

if( ! class_exists( 'Custom_Variable_Type_Controller' ) ) :
Class Custom_Variable_Type_Controller {

	/**
	 * Hold onto product type object
	 *
	 * @var Custom_Variable_Type
	 */
	protected $productType;


	/**
	 * Hold onto product type slug
	 *
	 * @var String
	 */
	protected $type_slug;


	/**
	 * Setup class properties
	 */
	protected function __construct() {

		$this->productType = new Custom_Variable_Type();
		$this->type_slug = $this->productType->get_type();
	}


	/**
	 * Initialize controller
	 *
	 * @return void
	 */
	public static function initialize() {

		$class = new self();
		$class->action_hooks();
		$class->filter_hooks();

		do_action( sprintf( '%s_initialize', strtolower( __CLASS__ ) ), $class );

	}



	/**------------------------------------------------------------------------------------------------ **/
	/** :: Action Hooks :: **/
	/**------------------------------------------------------------------------------------------------ **/
	/**
	 * Add any necessary action hooks
	 *
	 * @return void
	 */
	private function action_hooks() {

		// Frontend
		add_action( "woocommerce_{$this->type_slug}_add_to_cart", array( $this, 'variable_type_cart_template') );

		// Admin
		add_action( 'admin_enqueue_scripts',								array( $this, 'enqueue_assets' ) );
		add_action( "woocommerce_process_product_meta_{$this->type_slug}",	array( $this, 'sync_variation_prices' ) );

	}


	/**
	 * Frontend
	 * Single Product Page
	 * Have WooCommerce use the normal variable add to cart template
	 *
	 * @return void
	 */
	public function variable_type_cart_template() {
		woocommerce_variable_add_to_cart();
	}


	/**
	 * Admin
	 * Enqueue any necessary assets
	 *
	 * @return void
	 */
	public function enqueue_assets() {

		$screen = get_current_screen();
		if( empty( $screen ) || 'product' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_script(
			'custom-variable-type',
			\CVPT\Custom_Variable_Product_Type::get_asset_url( 'js/admin.js' ),
			array( 'jquery' ),
			'1.0.0',
			true
		);
		wp_localize_script( 'custom-variable-type', 'data', array(
			'product_type_slug' => $this->type_slug,
		) );

	}


	/**
	 * Admin
	 * Sync the variation prices
	 *
	 * @param Integer $product_id
	 *
	 * @return void
	 */
	public function sync_variation_prices( $product_id ) {

		$productType = wc_get_product( $product_id );
		$productType->variable_product_sync();

	}



	/**------------------------------------------------------------------------------------------------ **/
	/** :: Filter Hooks :: **/
	/**------------------------------------------------------------------------------------------------ **/
	/**
	 * Add any necessary filter hooks
	 *
	 * @return void
	 */
	private function filter_hooks() {

		// Frontend
		add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'define_cart_handler' ), 10, 2 );

		// Admin
		add_filter( 'woocommerce_data_stores',		array( $this, 'associate_product_datastore' ) );
		add_filter( 'woocommerce_product_class',	array( $this, 'add_product_type' ), 10, 2 );
		add_filter( 'product_type_selector',		array( $this, 'add_product_type_selector' ) );

	}


	/**
	 * Frontend
	 * Define how WooCommerce handles the cart and chekcout process.
	 *
	 * @param String $product_type
	 * @param WC_Product $product
	 *
	 * @return String $product_type
	 */
	function define_cart_handler( $product_type, $product ) {

		if( $product->is_type( $this->type_slug ) ) {
			$product_type = 'variable';
		}

		return $product_type;

	}


	/**
	 * Admin
	 * Associate our type with the Variable product datastore
	 *
	 * @param Array $stores	- Array( $slug => $class )
	 *
	 * @return Array $stores
	 */
	public function associate_product_datastore( $stores ) {

		$store_key = sprintf( 'product-%s', $this->type_slug );

		// Set our type to use the same Variable Product datastore.
		if( ! isset( $stores[ $store_key ] ) && isset( $stores['product-variable'] ) ) {
			$stores[ $store_key ] = $stores['product-variable'];
		}

		return $stores;

	}


	/**
	 * Admin
	 * Add product type to the acceptable product type classes
	 *
	 * @param String $classname
	 * @param String $product_type
	 *
	 * @return String $classname
	 */
	public function add_product_type( $classname, $product_type ) {

		if( $product_type === $this->type_slug ) {
			$classname = 'Custom_Variable_Type';
		}

		return $classname;

	}


	/**
	 * Admin
	 * Add product type to the selectable types when creating a new product
	 *
	 * @param Array $product_types
	 *
	 * @return Array $product_types
	 */
	public function add_product_type_selector( $product_types ) {

		if( ! isset( $product_types[ $this->type_slug ] ) ) {
			$product_types[ $this->type_slug ] = $this->productType->get_label( 'title' );
		}

		return $product_types;

	}

}
Custom_Variable_Type_Controller::initialize();
endif;