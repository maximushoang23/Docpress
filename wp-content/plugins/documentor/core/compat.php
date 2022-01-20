<?php 
//Added for WooCommerce plugin compatibility 
if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
	function woo_new_product_tab( $tabs ) {
		global $post;
		$pid = $post->ID; 
		if( !empty( $pid ) ) {
			$attachid = get_post_meta( $pid, '_documentor_attachid', true );
			if( !empty( $attachid ) ) {
				// Adds the new tab
				$tabs['desc_tab'] = array(
				'title'     => __( 'Documentation', 'woocommerce' ),
				'priority'  => 50,
				'callback'  => 'woo_new_product_tab_content'
				);
			}
		}
		return $tabs;
	}
	function woo_new_product_tab_content() {
		// The new tab content
		global $post;
		$pid = $post->ID;
		$attachid = get_post_meta( $pid, '_documentor_attachid', true ); 
		$attachid = intval( $attachid );
		if( !empty( $attachid ) ) {
			echo do_shortcode("[documentor ".$attachid."]");
		}
	}
}
//Added for : TablePress tables not appearing in generated PDF
add_action( 'init', 'load_tablepress_in_the_admin', 11 );
function load_tablepress_in_the_admin() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	    TablePress::$controller = TablePress::load_controller( 'frontend' );
	}
}
?>
