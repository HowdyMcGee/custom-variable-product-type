<?php
/**
 * Custom Variable Type Class
 * See WC_Product_Variable for extendable methods.
 */

if( ! defined( 'ABSPATH' ) ) {
	return;
}

if( ! class_exists( 'Custom_Variable_Type' ) ) :
Class Custom_Variable_Type extends \WC_Product_Variable {

	/**
	 * Setup product type
	 */
	public function __construct( $product = 0 ) {

		$this->product_type = 'custom_variable';
		parent::__construct( $product );

	}


	/**
	 * Override Parent Method
	 *
	 * @return String
	 */
	public function get_type() {
		return $this->product_type;
	}


	/**
	 * Return a specific label
	 *
	 * @param String $key
	 *
	 * @return String
	 */
	public function get_label( $key, $default = '' ) {

		$labels = array(
			'title' => esc_html__( 'Custom Variable' ),
		);

		return ( isset( $labels[ $key ] ) ) ? $labels[ $key ] : $default;

	}


	/**
	 * Sync variable product prices with the childs lowest/highest prices.
	 * Forcing sync will ensure the base product has _price metadata based
	 * on the variations.
	 *
	 * @param Integer $product_id
	 *
	 * @return void
	 */
	public function variable_product_sync( $product_id = 0 ) {

		if( empty( $product_id ) ) {
			$product_id = $this->get_id();
		}

		// Sync prices with variations.
		self::sync( $product_id );
	}

}
endif;