<?php
/**
 * Add Card
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes\Card;

use MG_Merchant_E_Solutions\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\AddCard' ) ) {

	/**
	 * Add Card
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class AddCard extends Classes\Gateway {

		/**
		 * ID.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var int
		 */
		protected static $static_id;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->hooks();
			self::$static_id = $this->id;
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
			add_action( 'woo_mes_' . $this->id . '_add_card', array( $this, 'add' ) );
		}

		/**
		 * Add.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @return array
		 */
		public function add() {
			$post = Classes\GetPost::request();

			$expiry       = isset( $post[ $this->id . '-card-expiry' ] ) ? $post[ $this->id . '-card-expiry' ] : '';
			$expiry_month = trim( strtok( $expiry, '/' ) );
			$expiry_year  = trim( substr( $expiry, strrpos( $expiry, '/' ) + 1 ) );

			// Make a 2 digit year 4 digits.
			if ( 2 === strlen( $expiry_year ) ) {
				$expiry_year = '20' . $expiry_year;
			}

			$post_data = array(
				'payment_method'     => isset( $post['payment_method'] ) ? $post['payment_method'] : '',
				'card_number'        => isset( $post[ $this->id . '-card-number' ] ) ? str_replace( ' ', '', $post[ $this->id . '-card-number' ] ) : '',
				'card_expiry'        => $expiry,
				'card_expiry_month'  => $expiry_month,
				'card_expiry_year'   => $expiry_year,
				'card_cvc'           => isset( $post[ $this->id . '-card-cvc' ] ) ? $post[ $this->id . '-card-cvc' ] : '',
				'credit_card_type'   => isset( $post[ $this->id . '-card-type' ] ) ? $post[ $this->id . '-card-type' ] : '',
				'add_payment_method' => isset( $post['woocommerce_add_payment_method-card-cvc'] ) ? $post['woocommerce_add_payment_method-card-cvc'] : 0,
				'billing_address_1'  => get_user_meta( get_current_user_id(), 'billing_address_1', true ),
				'billing_potalcode'  => get_user_meta( get_current_user_id(), 'billing_postcode', true ),
			);

			if ( 'yes' === $this->test_mode ) {
				$post_data['billing_address_1'] = '123';
				$post_data['billing_potalcode'] = '55555';
			}

			// Bail if $_POST is empty.
			if ( empty( $post ) ) {
				wc_add_notice( 'Credit Card information is incomplete!', 'error' );
				return array(
					'result'   => 'fail',
					'redirect' => '',
				);
			}

			$tran = new \TpgTransaction( $this->profile_id, $this->profile_key );

			$tran->setHost( $this->api_endpoint );
			$tran->setProfile( $this->profile_id, $this->profile_key );
			$tran->setRequestField( 'card_number', $post_data['card_number'] );
			$tran->setRequestField( 'card_exp_date', $post_data['card_expiry'] );
			$tran->setRequestField( 'transaction_amount', 0.00 );
			$tran->setRequestField( 'cvv2', $post_data['card_cvc'] );
			$tran->setRequestField( 'cardholder_street_address', $post_data['billing_address_1'] );
			$tran->setRequestField( 'cardholder_zip', $post_data['billing_potalcode'] );
			$tran->setRequestField( 'store_card', 'y' );

			$tran->execute();

			if ( $tran->isApproved() ) {
				do_action( 'woo_mes_' . $this->id . '_save_card', $tran, $post_data );

			}
		}

		/**
		 * Save.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param Object $tran         The transaction.
		 * @param array  $post_data  The post values array.
		 *
		 * @return array
		 */
		public static function save( $tran, $post_data ) {
			// If the request has been proccessed.
			if ( $tran->getResponseField( 'card_id' ) ) {
				$cart_token = $tran->getResponseField( 'card_id' );

				// Create new token.
				$token = new \WC_Payment_Token_CC();
				$token->set_token( $cart_token );
				$token->set_gateway_id( self::$static_id );
				$token->set_card_type( $post_data['credit_card_type'] );
				$token->set_last4( substr( $post_data['card_number'], -4 ) );
				$token->set_expiry_month( $post_data['card_expiry_month'] );
				$token->set_expiry_year( $post_data['card_expiry_year'] );

				// Get the User ID.
				if ( is_user_logged_in() ) {
					$token->set_user_id( get_current_user_id() );
				}

				// Save the token to the database.
				return $token->save();
			}
		}
	}
}
