<?php
/**
 * Acf.
 *
 * @package    MG_VIP_Customer
 * @subpackage MG_VIP_Customer/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_VIP_Customer\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\ACF' ) ) {

	/**
	 * Acf
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class ACF {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->options_page();
			$this->fields();
		}

		/**
		 * Options Page.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function options_page() {
			if ( function_exists( 'acf_add_options_page' ) ) {
				acf_add_options_sub_page(
					array(
						'page_title'  => __( 'VIP Customers', 'moore-and-giles' ),
						'menu_title'  => __( 'VIP Customers', 'moore-and-giles' ),
						'parent_slug' => 'woocommerce',
					)
				);
			}
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
			if ( function_exists( 'acf_add_local_field_group' ) ) {

				acf_add_local_field_group(
					array(
						'key'                   => 'group_5cd1a810701b7',
						'title'                 => 'WooCommerce – VIP Customer',
						'fields'                => array(
							array(
								'key'               => 'field_5cd1a8187e5a0',
								'label'             => 'Customer Emails',
								'name'              => 'mg_vip_customer_emails',
								'type'              => 'textarea',
								'instructions'      => 'Enter each VIP customer email separated by a comma.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'maxlength'         => '',
								'rows'              => '',
								'new_lines'         => '',
							),
							array(
								'key'               => 'field_5cd1a8327e5a1',
								'label'             => 'Coupons',
								'name'              => 'coupons',
								'type'              => 'post_object',
								'instructions'      => 'Select the Coupons you want to apply to the VIP customers',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'post_type' => array(
									0 => 'shop_coupon',
								),
								'taxonomy'          => '',
								'allow_null'        => 1,
								'multiple'          => 1,
								'return_format'     => 'id',
								'ui'                => 1,
							),
						),
						'location'              => array(
							array(
								array(
									'param'    => 'options_page',
									'operator' => '==',
									'value'    => 'acf-options-vip-customers',
								),
							),
						),
						'menu_order'            => 0,
						'position'              => 'normal',
						'style'                 => 'default',
						'label_placement'       => 'top',
						'instruction_placement' => 'label',
						'hide_on_screen'        => '',
						'active'                => true,
						'description'           => '',
					)
				);
			}
		}
	}
}
