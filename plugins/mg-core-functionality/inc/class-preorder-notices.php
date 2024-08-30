<?php

class MG_PreorderNotices {
	public function __construct() {
		add_action( 'shopp_order_success', array( $this, 'send_extra_notices' ), 10, 1 );
	}

	function send_extra_notices( $purchase ) {
		$has_delayed_items = false;

		$purchase->load_purchased();

		foreach ( $purchase->purchased as $item ) {
			$value = shopp_meta( $item->price, 'price', 'inventory-control-mode' );
			if ( 'preorder-online' === $value || 'backorder' === $value ) {
				$has_delayed_items = true;
			}
		}

		if ( $has_delayed_items ) {
			$purchase->email(
				"$purchase->firstname $purchase->lastname",
				$purchase->email,
				__( 'Worth the wait.', 'Shopp' ),
				array( 'email-extra-processing.php' )
			);
		}
	}
}
