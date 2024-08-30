<?php
/*
Plugin Name: MG Order Data Search
Plugin URI: http://clifgriffin.com
Description:  Time range reports on bestsellers. Order data search.
Version: 1.0
Author: Clif Griffin Development Inc.
Author URI: http://cgd.io
*/

add_action('admin_menu', 'mg_orderdata_admin_menu', 100);

function mg_orderdata_admin_menu() {
	add_submenu_page( "shopp-orders", "Order Search", "Order Data Search", "shopp_orders", "order-search", "mg_ordersearch_admin" );
}

function mg_ordersearch_admin() {
	include_once('mg-ordersearch-admin.php');
}