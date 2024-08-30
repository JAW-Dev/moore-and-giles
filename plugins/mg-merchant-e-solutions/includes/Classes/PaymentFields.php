<?php
/**
 * Payment Fields
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\PaymentFields' ) ) {

	/**
	 * Payment Fields
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class PaymentFields {

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
		 * Render.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string  $description The description.
		 * @param boolean $test_mode   Test mode.
		 * @param string  $mode        The transaction mode.
		 * @param string  $id          The ID.
		 * @param array   $supports    The types of actions supported.
		 *
		 * @return void
		 */
		public static function render( $description, $test_mode, $mode, $id, $supports ) {

			if ( 'yes' === $test_mode ) {
				$description .= ' ' . sprintf( __( 'TEST MODE ENABLED. Use a test card: %s<br/>Card: 4000700705251681 CVC: 123', 'woocommerce' ), '<a href="http://developer.merchante-solutions.com/#/payment-gateway-testing#card-numbers">developer.merchante-solutions.com</a>' ); // phpcs:ignore
			}

			if ( $description ) {
				echo wpautop( wptexturize( trim( $description ) ) ); // phpcs:ignore
			}

			$cc_form             = new \WC_Payment_Gateway_CC();

			if ( false && 'pg' === $mode ) {
				$cc_form->id         = $id;
				$cc_form->supports   = $supports;
				$cc_form->supports[] = 'tokenization';

				$cc_form->tokenization_script();
				$cc_form->saved_payment_methods();
				$cc_form->form();

				if ( is_checkout() ) {
					$cc_form->save_payment_method_checkbox();
				}
			} else {
				$cc_form->form();
			}
		}
	}
}
