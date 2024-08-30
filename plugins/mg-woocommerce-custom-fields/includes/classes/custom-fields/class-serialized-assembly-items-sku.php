<?php
/**
 * Serialized Assembly Items Sku.
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

if ( ! class_exists( __NAMESPACE__ . '\\Serialized_Assembly_Items_Sku' ) ) {

	/**
	 * Serialized Assembly Items Sku
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Serialized_Assembly_Items_Sku {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->fields();
			$this->variant_fields();
		}

		/**
		 * Fields.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function fields() {
			$field_args = array(
				'label'         => 'Serialized Assembly Sku',
				'id'            => 'serialized_assembly_items_sku',
				'wrapper_class' => 'woocommerce_product_options_serialized_assembly_items_sku',
				'class'         => 'serialized_assembly_items_sku',
				'desc_tip'      => true,
				'description'   => __( 'Add the product\'s Serialized Assembly SKU.', 'moore-and-giles' ),
			);

			$args = array(
				'hook'       => 'woocommerce_product_options_sku',
				'field_type' => 'text',
				'arguments'  => $field_args,
			);

			mg_wcf_create_field( $args );
		}

		/**
		 * Variant Fields.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function variant_fields() {
			$field_args = array(
				'label'         => 'Serialized Assembly Sku',
				'id'            => 'varaint_serialized_assembly_items_sku',
				'wrapper_class' => 'woocommerce_variation_options_serialized_assembly_items_sku form-row form-row-full',
				'class'         => 'serialized_assembly_items_sku',
				'desc_tip'      => true,
				'description'   => __( 'Add the product\'s Serialized Assembly SKU.', 'moore-and-giles' ),
			);

			$args = array(
				'hook'       => 'woocommerce_variation_options_pricing',
				'field_type' => 'text',
				'arguments'  => $field_args,
			);

			mg_wcf_create_field( $args );
		}
	}
}
