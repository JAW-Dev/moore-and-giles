<?php
/**
 * Enqueue Scripts.
 *
 * @package    MG_Order_Reports
 * @subpackage MG_Order_Reports/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2019, Objectiv
 * @license    GPL-2.0
 * @version    1.0.0
 * @since      1.0.0
 */

namespace MGOrderReports\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\EnqueueScripts' ) ) {

	/**
	 * Enqueue Scripts.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class EnqueueScripts {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
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
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		/**
		 * Enqueue Admin Scripts.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $hook The admin page hook.
		 *
		 * @example if( $hook != 'edit.php' ) { return }
		 *
		 * @return void
		 */
		public function admin_scripts( $hook ) {
			$file = 'dist/scripts/admin.js';

			if ( 'toplevel_page_mg-order-reports' !== $hook ) {
				return;
			}

			if ( file_exists( MG_ORDER_REPORTS_PLUGIN_DIR_PATH . $file ) ) {
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script(
					MG_ORDER_REPORTS_PLUGIN_PRFIX . '_scripts',
					MG_ORDER_REPORTS_PLUGIN_DIR_URL . $file,
					array( 'jquery' ),
					filemtime( MG_ORDER_REPORTS_PLUGIN_DIR_PATH . $file ),
					true
				);
				wp_localize_script(
					MG_ORDER_REPORTS_PLUGIN_PRFIX . '_scripts',
					$this->format_localized_script_name( MG_ORDER_REPORTS_PLUGIN_PRFIX ),
					array(
						'ajaxURL' => admin_url( 'admin-ajax.php' ),
					)
				);
			}
		}

		/**
		 * Format Localized Script Name
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $string The string to format.
		 *
		 * @return string
		 */
		public function format_localized_script_name( $string ) {
			preg_match_all( '/\_\s*\w/', $string, $matches );

			foreach ( $matches[0] as $match ) {
				$string = str_replace( $match, strtoupper( str_replace( '_', '', $match ) ), $string );
			}

			return $string;
		}
	}
}
