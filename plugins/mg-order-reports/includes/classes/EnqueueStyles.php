<?php
/**
 * Enqueue Styles.
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

if ( ! class_exists( __NAMESPACE__ . '\\EnqueueStyles' ) ) {

	/**
	 * Enqueue Styles.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class EnqueueStyles {

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
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		}

		/**
		 * Enqueue Admin Styles.
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
		public function admin_styles( $hook ) {
			$file = 'dist/styles/admin.css';

			if ( 'toplevel_page_mg-order-reports' !== $hook ) {
				return;
			}

			if ( file_exists( MG_ORDER_REPORTS_PLUGIN_DIR_PATH . $file ) ) {
				wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css', array(), filemtime( MG_ORDER_REPORTS_PLUGIN_DIR_PATH . $file ) );
				wp_enqueue_style( MG_ORDER_REPORTS_PLUGIN_PRFIX . '_stylesheet', MG_ORDER_REPORTS_PLUGIN_DIR_URL . $file, array(), filemtime( MG_ORDER_REPORTS_PLUGIN_DIR_PATH . $file ) );
			}
		}
	}
}
