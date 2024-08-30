<?php
/**
 * Save Card
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

if ( ! class_exists( __NAMESPACE__ . '\\SaveCard' ) ) {

	/**
	 * Save Card
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class SaveCard extends Classes\Gateway {

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
			add_action( 'woo_mes_' . $this->id . '_save_card', array( $this, 'save' ), 10, 3 );
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
