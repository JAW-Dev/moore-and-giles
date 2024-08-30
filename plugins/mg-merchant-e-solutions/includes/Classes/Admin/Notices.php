<?php
/**
 * Admin Notices
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes\Admin;

use MG_Merchant_E_Solutions\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Notices' ) ) {

	/**
	 * Admin Notices
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Notices extends Classes\Gateway {

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
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		/**
		 * Render.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function admin_notices() {
			if ( 'no' === $this->enabled ) {
				return;
			}

			$class = 'notice notice-error';

			// Check required fields.
			if ( ! $this->profile_id || ! $this->profile_key ) {
				$message = __( 'Merchant e-Solutions Error: Please enter your profile id and required info.', 'moore-and-giles' );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			// Show message when using standard mode and no SSL on the checkout page.
			if ( 'pg' === $this->mode && ! wc_checkout_is_https() ) {
				// translators: The admin checkout settings page.
				$message = sprintf( __( 'Merchant e-Solutions is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout may not be secure!', 'moore-and-giles' ), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}
	}
}
