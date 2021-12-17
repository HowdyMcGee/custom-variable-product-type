<?php
/**
 * Plugin Name: - Custom Variable Product Type
 * Description: Simple example of a custom variable product type.
 * Version: 1.0.0
 * Author: Howdy_McGee
 */
namespace CVPT;

if( ! defined( 'ABSPATH' ) ) {
	return;
}


/**
 * Main plugin controller
 */
if( ! class_exists( 'Custom_Variable_Product_Type' ) ) :
Class Custom_Variable_Product_Type {

	/**
	 * Hold onto root directory
	 *
	 * @var String
	 */
	private $root_dir;


	/**
	 * Hold onto root url
	 *
	 * @var String
	 */
	private $root_url;


	/**
	 * Initialize any necessary properties
	 */
	public function __construct() {

		$this->root_dir = plugin_dir_path( __FILE__ );
		$this->root_url = plugin_dir_url( __FILE__ );

		$this->include_files();

	}


	/**
	 * Include any necessary files
	 *
	 * @return void
	 */
	protected function include_files() {

		foreach( array(
			'includes/custom-variable-type',
			'includes/custom-variable-type-controller',
		) as $rel_path ) {
			include_once( sprintf( '%s/%s.php', $this->root_dir, $rel_path ) );
		}

	}


	/**
	 * Return a plugin directory URL
	 *
	 * @param String $rel_path	- String relative to the assets directory.
	 *
	 * @return String
	 */
	public static function get_asset_url( $rel_path ) {

		return sprintf( '%s/assets/%s', plugin_dir_url( __FILE__ ), $rel_path );

	}

}

add_action( 'plugins_loaded', function() {

	if( ! class_exists( 'WC_Product_Variable' ) ) {
		return;
	}

	new Custom_Variable_Product_Type();

} );

endif;