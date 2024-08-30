<?php
/*
Plugin Name: MG Inventory Update Batcher
Plugin URI: https://objectiv.co
Description: Batch inventory updates for asynchronous cron execution.
Version: 1.0.0
Author: Objectiv
Author URI: http://objectiv.co

------------------------------------------------------------------------
Copyright 2009-2020 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

/**
 * @param int $product_id
 * @param int $stock
 *
 * @return int
 */
function mg_add_item_to_batch( $product_id, $stock ) {
	global $wpdb;

	$wpdb->insert(
		"{$wpdb->prefix}inventory_update_batch_queue", array(
			'product_id' => $product_id,
			'stock'      => $stock,
		), array( '%d', '%d' )
	);

	return $stock;
}

function mg_inventory_update_batcher_activate() {
	/* Create Table */
	global $wpdb;
	$sql = "CREATE TABLE {$wpdb->prefix}inventory_update_batch_queue (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `product_id` bigint(20) DEFAULT 0,
		  `stock` bigint(20) DEFAULT 0,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	// Queue up the job
	if ( ! wp_next_scheduled( 'mg_inventory_update_batcher_run' ) ) {
		wp_schedule_event( time(), 'five_minutes', 'mg_inventory_update_batcher_run' );
	}
}

function mg_inventory_update_batcher_deactivate() {
	wp_clear_scheduled_hook( 'mg_inventory_update_batcher_run' );
}

add_action(
	'mg_inventory_update_batcher_run', function() {
		set_time_limit( 0 );

		global $wpdb;

		$items = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}inventory_update_batch_queue" );

		foreach ( $items as $item ) {
			wc_update_product_stock( $item->product_id, $item->stock );

			$wpdb->delete( "{$wpdb->prefix}inventory_update_batch_queue", array( 'id' => $item->id ) );
		}
	}
);

add_filter( 'cron_schedules', 'mg_add_five_minute_cron' );

function mg_add_five_minute_cron( $schedules ) {
	$schedules['five_minutes'] = array(
		'interval' => 5 * 60,
		'display'  => esc_html__( 'Every Five Minutes' ),
	);
	return $schedules;
}

register_activation_hook( __FILE__, 'mg_inventory_update_batcher_activate' );
register_deactivation_hook( __FILE__, 'mg_inventory_update_batcher_deactivate' );
