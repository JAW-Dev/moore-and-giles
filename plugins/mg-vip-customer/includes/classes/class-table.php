<?php
/**
 * Table.
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

// if ( class_exists( 'WC_Admin_List_Table_Orders', false ) ) {
// 	return;
// }

if ( ! class_exists( 'WC_Admin_List_Table', false ) ) {
	include_once WP_PLUGIN_DIR . '/woocommerce/includes/admin/list-tables/abstract-class-wc-admin-list-table.php';
}

// if ( ! class_exists( 'WC_Admin_List_Table_Orders', false ) ) {
// 	include_once WP_PLUGIN_DIR . '/woocommerce/includes/admin/list-tables/class-wc-admin-list-table-orders.php';
// }





if ( ! class_exists( __NAMESPACE__ . '\\Table' ) ) {

	/**
	 * Table
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Table {
	// class Table extends \WC_Admin_List_Table {
	// class Table extends \WC_Admin_List_Table_Orders {

		/**
		 * Args.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $args;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version                   The plugin version.
		 *     @type string $plugin_dir_url            The plugin directory URL.
		 *     @type string $plugin_dir_path           The plugin Directory Path.
		 *     @type string $field_id_code             The code field ID.
		 *     @type string $field_id_enable           The enable field ID.
		 *     @type string $field_id_included_coupons The included coupons field ID.
		 *     @type string $field_id_members          The members field ID.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->args = $args;
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
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'test' ), 10, 2 );
		}

		/**
		 * Name.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function test( $column, $post_id ) {
			$order     = new \WC_Order( $post_id );
			$email     = $order->get_billing_email();
			$vip_users = explode( ',', preg_replace( '/\s+/', '', $this->args['vip_members'] ) );

			foreach ( $vip_users as $vip_user ) {
				if ( ! empty( $vip_user ) ) {
					if ( 'order_number' === $column && $vip_user === $email ) {
						echo '<span class="mg-order-label">VIPffff</span>';
					}
				}
			}
		}
	}
}
