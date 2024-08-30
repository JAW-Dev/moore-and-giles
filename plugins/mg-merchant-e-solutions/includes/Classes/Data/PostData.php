<?php
/**
 * Post Data
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes\Data;

use MG_Merchant_E_Solutions\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\PostData' ) ) {

	/**
	 * Post Data
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class PostData extends Classes\Gateway {

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
		}

		/**
		 * Render.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $post The $post array.
		 *
		 * @return array
		 */
		public function get( $post ) {
			$expiry       = isset( $post[ $this->id . '-card-expiry' ] ) ? $post[ $this->id . '-card-expiry' ] : '';
			$expiry_month = trim( strtok( $expiry, '/' ) );
			$expiry_year  = trim( substr( $expiry, strrpos( $expiry, '/' ) + 1 ) );

			// Make a 2 digit year 4 digits.
			if ( 2 === strlen( $expiry_year ) ) {
				$expiry_year = '20' . $expiry_year;
			}

			$post_data = array(
				'payment_method'    => isset( $post['payment_method'] ) ? $post['payment_method'] : '',
				'payment_token'     => isset( $post[ 'wc-' . $this->id . '-payment-token' ] ) ? $post[ 'wc-' . $this->id . '-payment-token' ] : false,
				'credit_card_type'  => isset( $post[ $this->id . '-card-type' ] ) ? $post[ $this->id . '-card-type' ] : '',
				'card_number'       => isset( $post[ $this->id . '-card-number' ] ) ? str_replace( ' ', '', $post[ $this->id . '-card-number' ] ) : '',
				'card_expiry'       => isset( $post[ $this->id . '-card-expiry' ] ) ? $expiry_month . substr( $expiry_year, -2 ) : '',
				'card_expiry_month' => isset( $post[ $this->id . '-card-expiry' ] ) ? $expiry_month : '',
				'card_expiry_year'  => isset( $post[ $this->id . '-card-expiry' ] ) ? $expiry_year : '',
				'card_cvc'          => isset( $post[ $this->id . '-card-cvc' ] ) ? $post[ $this->id . '-card-cvc' ] : '',
				'save_to_account'   => isset( $post[ 'wc-' . $this->id . '-new-payment-method' ] ) ? $post[ 'wc-' . $this->id . '-new-payment-method' ] : false,
			);

			return ! empty( $post_data ) ? $post_data : array();
		}
	}
}
