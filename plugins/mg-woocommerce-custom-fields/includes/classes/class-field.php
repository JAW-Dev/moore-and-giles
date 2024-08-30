<?php
/**
 * Field.
 *
 * @package    MG_WooCommerce_Custom_Fields
 * @subpackage MG_WooCommerce_Custom_Fields/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_WooCommerce_Custom_Fields\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Field' ) ) {

	/**
	 * Fiels
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Field {

		/**
		 * Field Args.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $field_args;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Setup the custom field.
		 *
		 *     @type string  $hook       The WooCommerce field action hook.
		 *     @type string  $field_type The type of field to create.
		 *     @type array   $arguments  The arguments for the field.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->field_args = $this->get_args( $args );
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

			// Bail if fields args is empty.
			if ( empty( $this->field_args ) ) {
				return;
			}

			$field_args  = $this->field_args;
			$hook        = $field_args['hook'];
			$is_variable = $field_args['variable'];

			if ( $is_variable ) {
				add_action( $hook, array( $this, 'add_variable_product_field' ), 10, 3 );
				add_action( 'woocommerce_save_product_variation', array( $this, 'save_variable' ), 10, 2 );
				add_action( 'save', array( $this, 'save_variable' ), 10, 2 );
			} else {
				add_action( $hook, array( $this, 'add_product_field' ) );
				add_action( 'woocommerce_process_product_meta', array( $this, 'save_product' ) );
				add_action( 'save', array( $this, 'save_product' ), 10, 2 );
			}
		}

		/**
		 * Get Args.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args The field arguments.
		 *
		 * @return array
		 */
		public function get_args( $args ) {

			// Bail if arguments are not set.
			if ( ! isset( $args['hook'] ) || ! isset( $args['field_type'] ) || empty( $args['arguments'] ) ) {
				return;
			}

			$defaults = array(
				'hook'       => '',
				'field_type' => '',
				'arguments'  => array(),
			);

			$args             = wp_parse_args( $args, $defaults );
			$args['variable'] = $this->is_variable( $args['hook'] ) ? true : '';

			return $args;
		}

		/**
		 * Add Product Field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function add_product_field() {
			$this->field_type();
		}

		/**
		 * Add Variable Product Field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function add_variable_product_field( $loop, $variation_data, $variation ) {
			$this->field_type( $loop, $variation_data, $variation );
		}

		/**
		 * Field Type.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function field_type( $loop = null, $variation_data = null, $variation = null ) {

			// Bail if fields args is empty.
			if ( empty( $this->field_args ) ) {
				return;
			}

			$field_args = $this->field_args;

			// Bail if field_type is not set.
			if ( empty( $field_args['field_type'] ) ) {
				return;
			}
			$field_type = $field_args['field_type'];

			// Bail if arguments is not set.
			if ( empty( $field_args['arguments'] ) ) {
				return;
			}
			$field_arguments = $field_args['arguments'];

			// Bail if field id is not set.
			if ( empty( $field_arguments['id'] ) ) {
				return;
			}
			$field_id    = $field_arguments['id'];
			$is_variable = $field_args['variable'];
			$field       = false;

			if ( $is_variable ) {
				$field_args['arguments']['value'] = get_post_meta( $variation->ID, $field_args['arguments']['id'], true );
				$field_args['arguments']['id']    = $field_id . '[' . $loop . ']';
				$field_arguments                  = $field_args['arguments'];
			}

			$field_args['arguments']['type'] = $field_type;

			switch ( $field_type ) {
				case 'text':
					$field = woocommerce_wp_text_input( $field_arguments );
					break;
				case 'textarea':
					$field = woocommerce_wp_textarea_input( $field_arguments );
					break;
				case 'select':
					$field = woocommerce_wp_select( $field_arguments );
					break;
				case 'select':
					$field = woocommerce_wp_select( $field_arguments );
					break;
				case 'radio':
					$field = woocommerce_wp_radio( $field_arguments );
					break;
				case 'checkbox':
					$field = woocommerce_wp_checkbox( $field_arguments );
					break;
				case 'hidden':
					$field = woocommerce_wp_hidden_input( $field_arguments );
					break;
			}

			return $field;
		}

		/**
		 * Save Product.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $post_id The post ID.
		 *
		 * @return void
		 */
		public function save_product( $post_id ) {
			$post = mg_wcf_get_post();

			// Bail if the $_POST is empty.
			if ( empty( $post ) ) {
				return;
			}

			// Bail if fields args is empty.
			if ( empty( $this->field_args ) ) {
				return;
			}

			$field_args = $this->field_args;

			// Bail if arguments is not set.
			if ( empty( $field_args['arguments'] ) ) {
				return;
			}
			$field_arguments = $field_args['arguments'];

			// Bail if field id is not set.
			if ( empty( $field_arguments['id'] ) ) {
				return;
			}
			$field_id = isset( $field_arguments['id'] ) ? $field_arguments['id'] : '';

			$product   = wc_get_product( $post_id );
			$post_data = isset( $post[ $field_id ] ) ? $post[ $field_id ] : '';

			if ( $field_id ) {
				$product->update_meta_data( $field_id, $post_data );
				$product->save();
			}
		}

		/**
		 * Save Variable.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $variation_id The variation ID.
		 * @param int $i            The varation iteration.
		 *
		 * @return void
		 */
		public function save_variable( $variation_id, $i ) {
			$post = mg_wcf_get_post();

			// Bail if the $_POST is empty.
			if ( empty( $post ) ) {
				return;
			}

			// Bail if fields args is empty.
			if ( empty( $this->field_args ) ) {
				return;
			}

			$custom_fields = array(
				'varaint_disable_personalization',
				'varaint_serialized_assembly_items_sku',
				'variant_personalization_tooltip',
			);

			foreach ( $custom_fields as $custom_field ) {
				if ( ! isset( $post[ $custom_field ] ) ) {
					$post[ $custom_field ] = array( '' );
				}
			}

			$field_args = $this->field_args;

			// Bail if arguments is not set.
			if ( empty( $field_args['arguments'] ) ) {
				return;
			}
			$field_arguments = $field_args['arguments'];

			// Bail if field id is not set.
			if ( empty( $field_arguments['id'] ) ) {
				return;
			}
			$field_id = $field_arguments['id'];

			// Bail if the $_POST is empty.
			if ( empty( $post ) ) {
				return;
			}

			$post_data = isset( $post[ $field_id ][ $i ] ) ? $post[ $field_id ][ $i ] : '';

			if ( $post_data ) {
				update_post_meta( $variation_id, $field_id, $post_data );
			} else {
				update_post_meta( $variation_id, $field_id, '' );
			}
		}

		/**
		 * Is Variable.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $hook The WooCommerce field action hook.
		 *
		 * @return boolean
		 */
		public function is_variable( $hook = null ) {
			// Bail if hook is not null.
			if ( null === $hook ) {
				return;
			}

			if ( strpos( $hook, 'variation_options' ) !== false ) {
				return true;
			}

			return false;
		}
	}
}
