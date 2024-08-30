<?php
/**
 * Render Cart Addons
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

if ( ! class_exists( __NAMESPACE__ . '\\CartAddons' ) ) {

	/**
	 * Render Cart Addons
	 *
	 * This is the base class for rendering fields.
	 * It's purpose is to setup the properties.
	 * The core functionality is in class-render-fields.
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CartAddons extends RenderFields {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version         The plugin version.
		 *     @type string $plugin_dir_url  The plugin directory URL.
		 *     @type string $plugin_dir_path The plugin Directory Path.
		 *     @type array  $addons          The addons array.
		 * }
		 * @param int   $cart_item_product_id The cart item product ID.
		 * @param array $cart_item            The cart item array.
		 *
		 * @return void
		 */
		public function __construct( $args = array(), $cart_item_product_id = null, $cart_item = null ) {
			if ( ! $cart_item_product_id || empty( $cart_item ) ) {
				return;
			}
			$this->cart_item_product_id = $cart_item_product_id;
			$this->cart_item            = $cart_item;
			$this->cart_item_key        = $cart_item['key'];

			parent::__construct( $args );
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
			foreach ( $this->args['addons'] as $addon ) {
				if ( 'cart' === $addon['location'] ) {
					$this->addons[] = $addon;
				}
			}
		}
	}
}
