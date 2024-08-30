<?php
class MG_CheckoutValidation {
	public function __construct() {
		add_action('shopp_init', array($this, 'override_login_validation'), 1 );
		add_action('shopp_process_checkout', array($this, 'copy_names'), 1 );
		add_action('shopp_process_checkout', array($this, 'set_card_expire'), 1 );
	}

	function override_login_validation() {
		if ( isset($_POST['submit-login']) ) {
			$_POST['account-login'] = $_POST['email'];
		}
	}

	function copy_names() {

		$shipping_first_name = isset($_POST['info']['Shipping First Name']) ? $_POST['info']['Shipping First Name'] : '';
		$shipping_last_name = isset($_POST['info']['Shipping Last Name']) ? $_POST['info']['Shipping Last Name'] : '';

		// Set First Name
		if ( ! isset($_POST['firstname']) && ! empty($shipping_first_name) ) {
			$_POST['firstname'] = ShoppOrder()->firstname = $shipping_first_name;
		}

		// Set Last Name
		if ( ! isset($_POST['lastname']) && ! empty($shipping_last_name) ) {
			$_POST['lastname'] = ShoppOrder()->lastname = $shipping_last_name;
		}

		// Set Shipping Name
		if ( ! isset($_POST['shipping']['name']) && ! empty($shipping_first_name) && ! empty($shipping_last_name) ) {
			$_POST['shipping']['name'] = $shipping_first_name . ' ' . $shipping_last_name;
		}

		// Set Billing Name
		if ( ! isset($_POST['billing']['name']) && isset($_POST['firstname']) && isset($_POST['lastname']) ) {
			$_POST['billing']['name'] = $_POST['firstname'] . ' ' . $_POST['lastname'];
		}
	}

	function set_card_expire() {
		if ( ($expiration = ShoppOrder()->Checkout->form('billing-expiration')) !== false ) {
			if ( strlen($expiration) == 7 ) {
				$exmm = intval( substr($expiration, 0, 2) );
				$exyy = intval( substr($expiration, -2) );

				// For other validation purposes
				$_POST['billing']['cardexpires-mm'] = $exmm;
				$_POST['billing']['cardexpires-yy'] = $exyy;

				ShoppOrder()->Checkout->updateform();
			}
		}
	}
}
