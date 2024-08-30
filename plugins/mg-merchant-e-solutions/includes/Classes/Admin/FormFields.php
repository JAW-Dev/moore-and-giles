<?php
/**
 * Form Fields
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes\Admin;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\FormFields' ) ) {

	/**
	 * Form Fields
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class FormFields {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {}

		/**
		 * Get.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $id The ID.
		 *
		 * @return array
		 */
		public static function get( $id ) {
			return array(
				'enabled'     => array(
					'title'       => __( 'Enable/Disable', 'woocommerce' ),
					'label'       => __( 'Enable Merchant e-Solutions', 'woocommerce' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no',
				),
				'title'       => array(
					'title'       => __( 'Title', 'woocommerce' ),
					'type'        => 'text',
					'description' => __( 'This is the label the customer sees during checkout.', 'woocommerce' ),
					'default'     => __( 'Credit Card', 'woocommerce' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Description', 'woocommerce' ),
					'type'        => 'text',
					'description' => __( 'This controls the description which the customer sees during checkout.', 'woocommerce' ),
					'default'     => 'Pay with your credit card through Merchant e-Solutions\' secure Payment Gateway.',
					'desc_tip'    => true,
				),
				'mode'        => array(
					'title'       => __( 'Payment Mode', 'woocommerce' ),
					'type'        => 'select',
					'description' => sprintf( __( 'Choose PayHere to use the MeS PayHere API and redirect the customer to the MeS PayHere hosted page.', 'woocommerce' ), '<br />' ),
					'default'     => 'pg',
					'desc_tip'    => true,
					'options'     => array(
						'pg' => __( 'Payment Gateway', 'woocommerce' ),
						'ph' => __( 'PayHere', 'woocommerce' ),
					),
				),
				'profile_id'  => array(
					'title'       => __( 'Profile ID', 'woocommerce' ),
					'type'        => 'text',
					'description' => __( 'Get your API keys from your MeS account details page.', 'woocommerce' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'profile_key' => array(
					'title'       => __( 'Profile Key', 'woocommerce' ),
					'type'        => 'text',
					'description' => __( 'Get your API keys from your MeS account details page.', 'woocommerce' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'tran_type'   => array(
					'title'       => __( 'Transaction Type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Use the Payment Gateway to process Pre-Authorization or Sale transactions?', 'woocommerce' ),
					'default'     => 'sale',
					'desc_tip'    => true,
					'options'     => array(
						'sale' => __( 'Sale', 'woocommerce' ),
						'auth' => __( 'Pre-Authorization', 'woocommerce' ),
					),
				),
				'pre_order'   => array(
					'title'       => __( 'Pre-orders', 'woocommerce' ),
					'label'       => __( 'Disable Pre-order Processing', 'woocommerce' ),
					'type'        => 'checkbox',
					'desc_tip'    => true,
					'description' => __( 'Disable Merchant e-Solutions Pre-order processing', 'woocommerce' ),
					'default'     => 'no',
				),
				'pre_auth'   => array(
					'title'       => __( 'Pre-orders', 'woocommerce' ),
					'label'       => __( 'Disable Pre-authorization Processing', 'woocommerce' ),
					'type'        => 'checkbox',
					'desc_tip'    => true,
					'description' => __( 'Disable Merchant e-Solutions Pre-authorization', 'woocommerce' ),
					'default'     => 'no',
				),
				'test_mode'   => array(
					'title'       => __( 'Test Mode', 'woocommerce' ),
					'label'       => __( 'Enable Test Mode', 'woocommerce' ),
					'type'        => 'checkbox',
					'desc_tip'    => true,
					'description' => __( 'Place the payment gateway in test mode using your API keys (real payments will not be taken).', 'woocommerce' ),
					'default'     => 'yes',
				),
			);
		}
	}
}
