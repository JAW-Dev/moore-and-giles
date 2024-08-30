<?php
namespace Objectiv\Site\Woo;

class Emails {

	/**
	 * Set up the constructor for Emails class
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Add actions that we can use in our email templates
	 */
	public function add_actions() {
		add_action( 'mg_woo_email_general_text', array( $this, 'general_text' ), 22, 1 );
		add_action( 'mg_woo_email_ig_adventure', array( $this, 'email_ig_adventure' ), 22 );
		add_action( 'mg_woo_email_order_details', array( $this, 'email_order_details' ), 22, 1 );
		add_action( 'mg_woo_email_separator_line', array( $this, 'email_separator_line' ), 22 );
		add_action( 'mg_woo_email_simple_cta', array( $this, 'email_simple_cta' ), 22, 4 );
		add_action( 'mg_woo_email_top_hero', array( $this, 'email_top_hero' ), 22, 3 );
		add_action( 'mg_woo_email_tracking', array( $this, 'email_tracking' ), 22, 1 );
		add_action( 'mg_woo_email_top_hero_image', array( $this, 'email_top_hero_image' ), 22, 4 );
	}

	/**
	 * Outputs the Join Us on an Instagram Adventure section
	 */
	public function email_ig_adventure() {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template( 'emails/mg-pieces/ig-adventure.php' );
		}
	}

	/**
	 * Outputs the email "Top Hero" section
	 */
	public function email_top_hero( $title = null, $sub_title = null, $button = null ) {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template(
				'emails/mg-pieces/top-hero.php',
				array(
					'title'     => $title,
					'sub_title' => $sub_title,
					'button'    => $button,
				)
			);
		}
	}

	/**
	 * Outputs the email "Top Hero with Image" section
	 */
	public function email_top_hero_image( $title = null, $sub_title = null, $image_url = null, $button = null ) {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template(
				'emails/mg-pieces/top-hero-image.php',
				array(
					'title'     => $title,
					'sub_title' => $sub_title,
					'image_url' => $image_url,
					'button'    => $button,
				)
			);
		}
	}

	/**
	 * Outputs the email "Order Details" section
	 */
	public function email_order_details( $order = null ) {

		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template(
				'emails/mg-pieces/order-details.php',
				array(
					'order' => $order,
				)
			);
		}
	}

	/**
	 * Outputs the email "simple cta" section
	 */
	public function email_simple_cta( $title = null, $sub_title = null, $button = null, $alternative = false ) {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template(
				'emails/mg-pieces/simple-cta.php',
				array(
					'title'       => $title,
					'sub_title'   => $sub_title,
					'button'      => $button,
					'alternative' => $alternative,
				)
			);
		}
	}

	/**
	 * Outputs the email "simple cta" section
	 */
	public function general_text( $email_content = null ) {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template(
				'emails/mg-pieces/general-text.php',
				array(
					'email_content' => $email_content,
				)
			);
		}
	}

	/**
	 * Outputs a separation line
	 */
	public function email_separator_line() {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template( 'emails/mg-pieces/separator-line.php' );
		}
	}

	/**
	 * Outputs tracking info
	 */
	public function email_tracking( $order = null ) {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template(
				'emails/mg-pieces/tracking-section.php',
				array(
					'order' => $order,
				)
			);
		}
	}

	/**
	 * Outputs "extra" info for admin email
	 */
	public function admin_order_details( $order = null ) {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template(
				'emails/mg-pieces/admin-order-details.php',
				array(
					'order' => $order,
				)
			);
		}
	}
}
