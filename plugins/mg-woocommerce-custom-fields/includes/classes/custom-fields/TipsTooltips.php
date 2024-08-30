<?php
/**
 * Tips Tooltips.
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


/**
 * Tips Tooltips
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class TipsTooltips {

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
		// $this->variant_fields(); keep just in case
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
		// Shipping
		$field_args = array(
			'label'         => 'Shipping Tooltip Label',
			'id'            => 'tips_shipping_label_tooltip',
			'wrapper_class' => 'woocommerce_product_options_advanced__tips_shipping_label_tooltip',
			'class'         => 'short tips_shipping_label_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Shipping tooltip label.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_product_options_advanced',
			'field_type' => 'text',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );

		$field_args = array(
			'label'         => 'Shipping Tooltip',
			'id'            => 'tips_shipping_tooltip',
			'wrapper_class' => 'woocommerce_product_options_advanced__tips_shipping_tooltip',
			'class'         => 'short tips_shipping_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Shipping tooltip.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_product_options_advanced',
			'field_type' => 'textarea',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );

		// Product Care
		$field_args = array(
			'label'         => 'Product Care Tooltip Label',
			'id'            => 'tips_product_care_label_tooltip',
			'wrapper_class' => 'woocommerce_product_options_advanced__tips_product_care_label_tooltip',
			'class'         => 'short tips_product_care_label_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Product Care tooltip.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_product_options_advanced',
			'field_type' => 'text',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );

		$field_args = array(
			'label'         => 'Product Care Tooltip',
			'id'            => 'tips_product_care_tooltip',
			'wrapper_class' => 'woocommerce_product_options_advanced__tips_product_care_tooltip',
			'class'         => 'short tips_product_care_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Product Care tooltip.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_product_options_advanced',
			'field_type' => 'textarea',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );

		// Help
		$field_args = array(
			'label'         => 'Help Tooltip Label',
			'id'            => 'tips_help_label_tooltip',
			'wrapper_class' => 'woocommerce_product_options_advanced__tips_help_label_tooltip',
			'class'         => 'short tips_help_label_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Help tooltip label.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_product_options_advanced',
			'field_type' => 'text',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );

		$field_args = array(
			'label'         => 'Help Tooltip',
			'id'            => 'tips_help_tooltip',
			'wrapper_class' => 'woocommerce_product_options_advanced__tips_help_tooltip',
			'class'         => 'short tips_help_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Help tooltip.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_product_options_advanced',
			'field_type' => 'textarea',
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
		// Shipping
		$field_args = array(
			'label'         => 'Shipping Tooltip',
			'id'            => 'variant_tips_shipping_tooltip',
			'wrapper_class' => 'form-field form-row form-row-full woocommerce_variation_options_pricing__tips_shipping_tooltip',
			'class'         => 'short tips_shipping_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Shipping tooltip.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_variation_options_pricing',
			'field_type' => 'textarea',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );

		// Product Care
		$field_args = array(
			'label'         => 'Product Care Tooltip',
			'id'            => 'variant_tips_product_care_tooltip',
			'wrapper_class' => 'form-field form-row form-row-full woocommerce_variation_options_pricing__tips_product_care_tooltip',
			'class'         => 'short tips_product_care_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the Product Care tooltip.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_variation_options_pricing',
			'field_type' => 'textarea',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );

		// Help
		$field_args = array(
			'label'         => 'Help Tooltip',
			'id'            => 'variant_tips_help_tooltip',
			'wrapper_class' => 'form-field form-row form-row-full woocommerce_variation_options_pricing__tips_help_tooltip',
			'class'         => 'short tips_help_tooltip',
			'desc_tip'      => true,
			'description'   => __( 'The text used for the tips Help tooltip.', 'moore-and-giles' ),
		);

		$args = array(
			'hook'       => 'woocommerce_variation_options_pricing',
			'field_type' => 'textarea',
			'arguments'  => $field_args,
		);

		mg_wcf_create_field( $args );
	}
}
