<?php
class MG_LimitCartQuantityLeather {
	public function __construct() {
		if ( current_user_can( 'mg_impersonate_user' ) ) {
			return;
		}

		add_action( 'shopp_cart_add_item', array( $this, 'prevent_overage' ), 10, 1 );
	}

	public function prevent_overage( $Item ) {
		$max_allowed = 50;

		$quantity = intval( shopp( 'cart.get-total-quantity' ) );

		if ( $quantity > $max_allowed ) {
			ShoppOrder()->Cart->rmvitem( $Item->fingerprint() );
			return new ShoppError(Shopp::__("The product could not be added to the cart because it exceeds the maximum allowed number of items. ({$max_allowed})"), 'cart_item_quantity_exceeded', SHOPP_ERR);
		}
	}
}
