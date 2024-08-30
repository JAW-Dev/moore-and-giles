<?php
/**
 * User Token
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

if ( ! class_exists( __NAMESPACE__ . '\\UserToken' ) ) {

	/**
	 * User Token
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class UserToken {

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
		 * @param string $id    The ID.
		 * @param string $last4 The last four of the current payment token.
		 *
		 * @return object
		 */
		public static function get( $id, $last4 = null ) {
			$customer_token = null;

			if ( is_user_logged_in() ) {
				$tokens = \WC_Payment_Tokens::get_customer_tokens( get_current_user_id() );
				foreach ( $tokens as $token ) {
					$token_data  = $token->get_data();
					$token_last4 = $token_data['last4'];
					if ( null !== $last4 ) {
						if ( $token->get_gateway_id() === $id && $token_last4 === $last4 ) {
							$customer_token = $token;
						}
					} elseif ( $token->get_gateway_id() === $id && $token->is_default() ) {
						$customer_token = $token;
					}
				}
			}
			return $customer_token;
		}
	}
}
