<?php
class MG_CustomerMatching {
	public function __construct() {
		add_action('shopp_init', array($this, 'init_process_checkout'), 0);
		add_action('shopp_process_checkout', array($this, 'load_customer_before_checkout'), 9);
	}

	function init_process_checkout() {
        // Exclude OSR orders
		if ( isset($_POST['data']['_intended_recipient']) ) return;

		remove_action( 'shopp_process_checkout', array(ShoppOrder()->Checkout, 'data') );
		add_action( 'shopp_process_checkout', array(ShoppOrder()->Checkout, 'data'), 1 );
	}

	function load_customer_before_checkout() {
		global $wpdb;
		
		// If user can impersonate, or has impersonated, bail!
		// Unless of course they are impersonating a new customer
		if ( current_user_can('mg_impersonate_user') &&
            isset(ShoppOrder()->data['Impersonated Customer']) &&
            ShoppOrder()->data['Impersonated Customer'] != "new" ) {
                return;
        }

		// Make sure we haven't already done this
		if ( isset( ShoppOrder()->data['_matched_customer_email'] ) ) return;

		// Try to find another customer with this email address
		$prefix = $wpdb->get_blog_prefix();
		$destination_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$prefix}shopp_customer WHERE email = %s ORDER BY id ASC LIMIT 1", ShoppOrder()->Checkout->form('email') ) );

		// If we have a customer, load it
		if ( $destination_id !== false && $destination_id != ShoppOrder()->Customer->id ) {
			ShoppOrder()->Customer->load( $destination_id );
			unset(ShoppOrder()->Customer->password);

			ShoppOrder()->data['_matched_customer_id'] = $destination_id;
			ShoppOrder()->data['_matched_customer_email'] = ShoppOrder()->Customer->email;
		}
	}
}
