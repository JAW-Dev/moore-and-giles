<?php
/**
 * Methods.
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

if ( ! class_exists( __NAMESPACE__ . '\\Methods' ) ) {

	/**
	 * Methods
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Methods {

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
			$all_methods = new Shipping_Methods_Settings();
			$get_methods = $all_methods->get();
			$methods     = array( '' => ' ' );

			// Bail if empty.
			if ( empty( $get_methods ) ) {
				return;
			}

			foreach ( $get_methods as $key => $value ) {
				$title = isset( $value['title'] ) ? $value['title'] : '';

				// Continue if not set.
				if ( ! $title ) {
					continue;
				}

				$methods[ $key ] = $title;
			}

			return woocommerce_wp_select(
				array(
					'id'          => $this->args['field_id_method'],
					'desc_tip'    => true,
					'description' => __( 'Select the shippping method to apply the coupon to', 'moore-and-giles' ),
					'label'       => __( 'Shipping Method', 'moore-and-giles' ),
					'options'     => $methods,
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

			$field = $this->args['field_id_method'];
			$new   = isset( $post[ $field ] ) ? $post[ $field ] : '';
			$old   = metadata_exists( 'post', $post_id, $field ) ? get_post_meta( $post_id, $field ) : false;

			$enable     = $this->args['field_id_enable'];
			$new_enable = isset( $post[ $enable ] );
			$old_enable = metadata_exists( 'post', $post_id, $enable ) ? get_post_meta( $post_id, $enable ) : false;
			$is_enabled = $new_enable || 'yes' === $old_enable ? true : false;

			if ( $old && ! empty( $new ) && $new !== $old && $is_enabled ) {
				update_post_meta( $post_id, $field, $new );
			} elseif ( ! empty( $new ) && ! $old && $is_enabled ) {
				add_post_meta( $post_id, $field, $new, true );
			} elseif ( empty( $new ) && $old || ! $is_enabled ) {
				delete_post_meta( $post_id, $field );
			}
		}
	}
}
