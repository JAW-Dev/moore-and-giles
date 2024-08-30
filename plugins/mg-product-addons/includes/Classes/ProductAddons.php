<?php
/**
 * Render Product Addons
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

if ( ! class_exists( __NAMESPACE__ . '\\ProductAddons' ) ) {

	/**
	 * Render Product Addons
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class ProductAddons extends RenderFields {

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
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
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
				if ( 'product' === $addon['location'] ) {
					$this->addons[] = $addon;
					add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render_fields' ) );
				}
			}
		}
	}
}
