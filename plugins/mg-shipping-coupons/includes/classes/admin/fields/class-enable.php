<?php
/**
 * Enable.
 *
 * @package    MG_Shipping_Coupons
 * @subpackage MG_Shipping_Coupons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Shipping_Coupons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Enable' ) ) {

	/**
	 * Enable
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Enable {

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
		 *     @type string $version          The plugin version.
		 *     @type string $plugin_dir_url   The plugin directory URL.
		 *     @type string $plugin_dir_path  The plugin Directory Path.
		 *     @type string $field_id_code    The code field ID.
		 *     @type string $field_id_enable  The enable field ID.
		 *     @type string $field_id_method  The method field ID.
		 *     @type string $field_id_service The service field ID.
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
			add_action( 'woocommerce_coupon_options_save', array( $this, 'save' ) );
		}

		/**
		 * Render Field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return string
		 */
		public function render() {
			return woocommerce_wp_checkbox(
				array(
					'id'          => $this->args['field_id_enable'],
					'label'       => __( 'Apply to Shipping', 'moore-and-giles' ),
					'desc_tip'    => true,
					'description' => __( 'Apply coupon to shipping', 'moore-and-giles' ),
				)
			);
		}

		/**
		 * Save.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $post_id the post ID.
		 *
		 * @return void
		 */
		public function save( $post_id ) {
			$post = Post::request();

			// Bail if post is empty.
			if ( empty( $post ) ) {
				return;
			}

			$field = $this->args['field_id_enable'];
			$new   = isset( $post[ $field ] ) ? $post[ $field ] : '';
			$old   = metadata_exists( 'post', $post_id, $field ) ? get_post_meta( $post_id, $field ) : false;

			if ( $old && $new !== $old && '' !== $new ) {
				update_post_meta( $post_id, $field, $new );
			} elseif ( $new && ! $old ) {
				add_post_meta( $post_id, $field, $new, true );
			} elseif ( ! $new ) {
				delete_post_meta( $post_id, $field );
			}
		}
	}
}
