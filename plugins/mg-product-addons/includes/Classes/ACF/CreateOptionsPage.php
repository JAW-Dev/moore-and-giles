<?php
/**
 * Create ACF Options Page
 *
 * @package    MG_Product_Addons
 * @subpackage MG_Product_Addons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\CreateOptionsPage' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CreateOptionsPage {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->create_page();
		}

		/**
		 * Create Page.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function create_page() {
			if ( function_exists( 'acf_add_options_page' ) ) {
				acf_add_options_page(
					array(
						'page_title'  => 'Product Addons',
						'menu_title'  => 'Product Addons',
						'menu_slug'   => 'mg-product-addons',
						'capability'  => 'manage_options',
						'parent_slug' => 'edit.php?post_type=product',
					)
				);
			}
		}
	}
}
