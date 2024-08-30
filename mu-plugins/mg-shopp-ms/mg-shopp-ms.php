<?php
/*
Plugin Name: MG Shopp MS
Description:  Common address and customer tables for all stores in multisite.
Version: 1.0
Author: Clif Griffin Development Inc.
Author URI: http://cgd.io
*/

define('MG_BAGS', 1);
define('MG_LEATHER', 3);

class MG_ShoppMS {
	public function __construct() {
		add_filter( 'shopp_table_name', array($this, 'change_table_name'), 10, 2 );

		add_action('plugins_loaded', array($this, 'change_cookie_prefixes'), 0 );
	}

	function change_table_name( $table_name, $table ) {
		global $wpdb;

		if ( $table == "address" || $table == "customer" ) {
			$new_table = str_replace($wpdb->get_blog_prefix(), $wpdb->base_prefix, $table_name);
			return $new_table;
		}

		return $table_name;
	}

	function change_cookie_prefixes() {
		global $blog_id;

		if ( $blog_id > 1 ) {
			if ( ! defined('SHOPP_SECURE_KEY') )
				define('SHOPP_SECURE_KEY', 'shopp_sec_' . $blog_id . '_' . COOKIEHASH);

			if ( ! defined('SHOPP_SESSION_COOKIE') )
				define('SHOPP_SESSION_COOKIE', 'wp_shopp_' . $blog_id . '_' . COOKIEHASH);	
		}
	}
}

$MG_ShoppMS = new MG_ShoppMS();

function mg_is_bag_site() {
	global $blog_id;

	return ($blog_id == MG_BAGS);
}

function mg_is_leather_site() {
	global $blog_id;

	return ($blog_id == MG_LEATHER);
}
