<?php
/**
 * Custom Fields Meta.
 *
 * @package    MG_WooCommerce_Custom_Fields
 * @subpackage MG_WooCommerce_Custom_Fields/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_WooCommerce_Custom_Fields\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Custom_Fields_Meta' ) ) {

	/**
	 * Custom Fields Meta
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Custom_Fields_Meta {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->hooks();
		}

		/**
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_filter( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_data_serialized_sku' ), 25, 4 );
		}

		/**
		 * Order Item Data
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $item          The product object.
		 * @param int    $cart_item_key The product cart key.
		 * @param array  $values        The values array.
		 * @param object $order         The order object.
		 *
		 * @return void
		 */
		public function add_order_item_data_serialized_sku( $item, $cart_item_key, $values, $order ) {
			$product    = $item->get_product();
			$parent_id  = $product->get_parent_id();
			$product_id = $product->get_id();
			$variant    = $parent_id > 0 ? true : false;

			if ( ! $variant ) {
				$sku = get_post_meta( $product_id, 'serialized_assembly_items_sku', true );
				$item->update_meta_data( '_mg_serialized_assembly_item_sku', $sku );
			} else {
				$sku = get_post_meta( $product_id, 'varaint_serialized_assembly_items_sku', true );
				$item->update_meta_data( '_mg_serialized_assembly_item_sku', $sku );
			}
		}
	}
}
