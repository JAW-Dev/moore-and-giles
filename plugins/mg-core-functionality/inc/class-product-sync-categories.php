<?php
class MG_ProductSyncCategories {
	public function __construct() {
		add_action( 'wp_ajax_nopriv_product_sync_categories', array( $this, 'get_product_sync_categories' ) );
		add_action( 'wp_ajax_nopriv_product_sync_gift_wrapping', array( $this, 'get_product_sync_gift_wrapping' ) );
	}

	function get_product_sync_categories() {
		if ( ! empty($_REQUEST['product_id']) ) { // phpcs:ignore
			$product_id = intval( $_REQUEST['product_id'] ); // phpcs:ignore
			$categories = wp_get_post_terms( $product_id, 'shopp_category' );
			echo maybe_serialize( $categories ); // phpcs:ignore
		}

		die;
	}

	function get_product_sync_gift_wrapping() {
		if ( ! empty($_REQUEST['product_id']) ) { // phpcs:ignore
			global $MG_GiftWrapping; // phpcs:ignore

			$product_id = intval( $_REQUEST['product_id'] ); // phpcs:ignore
			$product    = shopp_product( $product_id );

			echo $MG_GiftWrapping->get_gift_wrapping_size( $product ); // phpcs:ignore
		}

		die;
	}
}
