<?php
/*
Plugin Name: MG Shopp IMOM
Description:  Re-route international orders.
Version: 1.0
Author: Clif Griffin Development Inc.
Author URI: http://cgd.io
*/

class MG_ShoppIMOM {
	function __construct() {
		// silence is golden
	}

	function start() {
		if ( stripos(get_site_url(), 'www.mooreandgiles.com') !== false ) {
			add_action('shopp_order_success', array($this, 'process_order'), 2, 1);
		}

		add_action('admin_notices', array($this, 'show_cs_notice'), 100 );
	}

	function process_order( $Purchase ) {

		if ( ! empty($Purchase->shipcountry) && $Purchase->shipcountry != "US" ) {

			// Unhook theme notifications
			//remove_action('shopp_order_success', 'internal_notify');

			$newstatus = 5; // should map to Customer Service Review

			$Purchase->status = $newstatus;
			$Purchase->save();

			/*$Purchase->email(
				'',
				"shop@mooreandgiles.com",
				__('New International Order','Shopp'),
				array("email-merchant-order.php")
			);*/
		}
	}

	function show_cs_notice() {
		if ( empty($_GET['page']) || $_GET['page'] != "shopp-orders" || empty($_GET['id']) ) return;

		$LePurchase = new ShoppPurchase( (int)$_GET['id'] );

		if ( ! empty($LePurchase) && $LePurchase->status == 5 ): ?>
	    <div class="error">
	        <p>This order is marked as under Customer Service Review. Seek permission before making any changes.</p>
	    </div>
	    <?php endif;
	}
}

$MG_ShoppIMOM = new MG_ShoppIMOM();
$MG_ShoppIMOM->start();