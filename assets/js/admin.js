/**
 * Admin: New Product
 */
( function( $ ) {

	/**
	 * Show all elements that are specificly variable product types
	 */
	function maybe_show_variable_elms() {

		if( ! $( '#product-type' ).length || $( '#product-type' ).val() !== data.product_type_slug ) {
			return;
		}

		$( '.show_if_variable' ).each( function() {
			$( this ).show();
		} );
	}


	/**
	 * General listeners to check if we need to show/hide our type elements
	 */
	$( document ).ready( function() {
		$( document.body ).on( 'woocommerce_added_attribute', (e) => maybe_show_variable_elms() );
		$( '#product-type' ).on( 'change', (e) => maybe_show_variable_elms() );
		$( '.save_attributes' ).on( 'click', (e) => maybe_show_variable_elms() );
		$( '#variable_product_options' ).on( 'reload', (e) => maybe_show_variable_elms() );
		$( '#woocommerce-product-data' ).on( 'woocommerce_variations_added woocommerce_variations_loaded', (e) => maybe_show_variable_elms() );
		maybe_show_variable_elms();
	} );

} )( jQuery );