<?php
/**
 * Processing.
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

if ( ! class_exists( __NAMESPACE__ . '\\Processing' ) ) {

	/**
	 * Processing
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Processing {

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
		 * @return void
		 */
		public function __construct() {
			$this->field_args = array(
				'label'       => __( 'Processing Time', 'moore-and-giles' ),
				'placeholder' => __( '24 hours', 'moore-and-giles' ),
				'class'       => 'processing-time',
				'desc_tip'    => true,
				'description' => __( 'The processing time for the product.', 'moore-and-giles' ),
			);

			$this->processing();
			$this->processing_variable();
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
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Process from.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function processing() {

			$from_args = array(
				'hook'       => 'woocommerce_product_options_shipping',
				'field_type' => 'select',
				'arguments'  => array(
					'label'         => 'Processing Minimum',
					'id'            => 'processing_time__from',
					'wrapper_class' => 'woocommerce-product-options-processing__from',
					'class'         => 'short processing-time__from',
					'options'       => $this->timeframes_menu(),
				),
			);

			$to_args = array(
				'hook'       => 'woocommerce_product_options_shipping',
				'field_type' => 'select',
				'arguments'  => array(
					'label'         => 'Processing Maximum',
					'id'            => 'processing_time__to',
					'wrapper_class' => 'woocommerce-product-options-processing__to',
					'class'         => 'short processing-time__to',
					'options'       => $this->timeframes_menu(),
				),
			);

			$prefix_args = array(
				'hook'       => 'woocommerce_product_options_shipping',
				'field_type' => 'text',
				'arguments'  => array(
					'label'         => 'Processing Prefix',
					'id'            => 'processing_time__prefix',
					'wrapper_class' => 'woocommerce-product-options-processing__prefix',
					'class'         => 'short processing-time__prefix',
					'placeholder'   => __( 'Prefix (optional e.g: Usually Ships in)', 'moore-and-giles' ),
				),
			);

			$date_args = array(
				'hook'       => 'woocommerce_product_options_shipping',
				'field_type' => 'text',
				'arguments'  => array(
					'label'         => 'Release Date',
					'id'            => 'release_date',
					'placeholder'   => __( '12/01/2019', 'moore-and-giles' ),
					'wrapper_class' => 'woocommerce-product-options-release-date',
					'class'         => 'short release-date',
					'desc_tip'      => true,
					'description'   => __( 'Select the date the product will be released.', 'moore-and-giles' ),
				),
			);

			$date_range_args = array(
				'hook'       => 'woocommerce_product_options_shipping',
				'field_type' => 'checkbox',
				'arguments'  => array(
					'label'         => 'Show release date as date range (i.e., in 1 week)',
					'id'            => 'release_date_range',
					'wrapper_class' => 'woocommerce-product-options-release-date',
					'class'         => 'release-date-range',
					'desc_tip'      => true,
					'description'   => __( 'If unchecked, the actual date will be displayed (i.e., on 07/04/2030)', 'moore-and-giles' ),
				),
			);

			mg_wcf_create_field( $from_args );
			mg_wcf_create_field( $to_args );
			mg_wcf_create_field( $prefix_args );
			mg_wcf_create_field( $date_args );
			mg_wcf_create_field( $date_range_args );
		}

		/**
		 * Process from.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function processing_variable() {

			$from_args = array(
				'hook'       => 'woocommerce_variation_options_inventory',
				'field_type' => 'select',
				'arguments'  => array(
					'label'         => 'Processing Minimum',
					'id'            => 'processing_time__from',
					'wrapper_class' => 'woocommerce-product-options-processing__from',
					'class'         => 'short processing-time__from',
					'options'       => $this->timeframes_menu(),
				),
			);

			$to_args = array(
				'hook'       => 'woocommerce_variation_options_inventory',
				'field_type' => 'select',
				'arguments'  => array(
					'label'         => 'Processing Maximum',
					'id'            => 'processing_time__to',
					'wrapper_class' => 'woocommerce-product-options-processing__to',
					'class'         => 'short processing-time__to',
					'options'       => $this->timeframes_menu(),
				),
			);

			$prefix_args = array(
				'hook'       => 'woocommerce_variation_options_inventory',
				'field_type' => 'text',
				'arguments'  => array(
					'label'         => 'Processing Prefix',
					'id'            => 'processing_time__prefix',
					'wrapper_class' => 'woocommerce-product-options-processing__prefix',
					'class'         => 'short processing-time__prefix',
					'placeholder'   => __( 'Prefix (optional e.g: Usually Ships in)', 'moore-and-giles' ),
				),
			);

			$date_args = array(
				'hook'       => 'woocommerce_variation_options_inventory',
				'field_type' => 'text',
				'arguments'  => array(
					'label'         => 'Release Date',
					'id'            => 'release_date',
					'placeholder'   => __( '12/01/2019', 'moore-and-giles' ),
					'wrapper_class' => 'woocommerce-product-options-release-date',
					'class'         => 'short release-date',
					'desc_tip'      => true,
					'description'   => __( 'Select the date the product will be released.', 'moore-and-giles' ),
				),
			);

			$date_range_args = array(
				'hook'       => 'woocommerce_variation_options_inventory',
				'field_type' => 'checkbox',
				'arguments'  => array(
					'label'         => 'Show release date as date range (i.e., in 1 week)',
					'id'            => 'release_date_range',
					'wrapper_class' => 'woocommerce-product-options-release-date',
					'class'         => 'short release-date-range',
					'desc_tip'      => true,
					'description'   => __( 'If unchecked, the actual date will be displayed (i.e., on 07/04/2030)', 'moore-and-giles' ),
				),
			);

			mg_wcf_create_field( $from_args );
			mg_wcf_create_field( $to_args );
			mg_wcf_create_field( $prefix_args );
			mg_wcf_create_field( $date_args );
			mg_wcf_create_field( $date_range_args );
		}

		public static function timeframes_menu() {
			$units = array(
				'd' => 32,
				'w' => 54,
				'm' => 13,
			);
			$_     = array();
			$min   = 0;

			foreach ( $units as $u => $count ) {
				for ( $i = $min; $i < $count; $i++ ) {
					switch ( $u ) {
						case 'd':
							$_[ $i . $u ] = sprintf( _n( '%d day', '%d days', $i, 'Shopp' ), $i );
							break;
						case 'w':
							$_[ $i . $u ] = sprintf( _n( '%d week', '%d weeks', $i, 'Shopp' ), $i );
							break;
						case 'm':
							$_[ $i . $u ] = sprintf( _n( '%d month', '%d months', $i, 'Shopp' ), $i );
							break;
							break;
					}
				}
				$min = ( 0 === $min ) ? ++$min : $min; // Increase the min number of units to one after the first loop (allow 0 days but not 0 weeks)
			}

			return apply_filters( 'shopp_timeframes_menu', $_ );
		}

		/**
		 * Field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function variable_field() {
			$this->field_args['id']            = 'variation_processing_time';
			$this->field_args['wrapper_class'] = 'product-processing form-row form-row-full';

			$args = array(
				'hook'       => 'woocommerce_variation_options_inventory',
				'field_type' => 'text',
				'arguments'  => $this->field_args,
			);

			mg_wcf_create_field( $args );
		}

		/**
		 * Enqueue Scripts.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function enqueue_scripts( $hook ) {
			$post_type = 'product';
			$pages     = array(
				'post.php',
				'post-new.php',
			);

			if ( in_array( $hook, $pages, true ) ) {
				$screen = get_current_screen();
				if ( is_object( $screen ) && $post_type === $screen->post_type ) {
					wp_enqueue_script( 'jquery-ui-datepicker' );
					wp_enqueue_style( 'jquery-ui-datepicker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
				}
			}
		}
	}
}
