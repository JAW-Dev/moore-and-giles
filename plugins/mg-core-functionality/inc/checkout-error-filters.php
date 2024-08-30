<?php
class MG_CheckoutErrorFilters {
	public function __construct() {
		add_filter('shopp_authorizenet_error', array($this, 'translate_error'), 10, 2);
	}

	function translate_error($message, $code) {
		switch($code) {
			// AVS Mismatch
			case "27":
				$message = "Transaction declined: The billing address provided does not match billing address of cardholder.";
				break;
			case "11":
				$message = "Transaction declined: Identical card information and amount submitted within last 2 minutes.";
				break;
			case "65":
				$message = "Transaction declined: The card security code (CVV) provided is invalid.";
				break;
			default:
				$message = "Transaction declined: Please verify credit card and billing address information.";
		}

		$message = sprintf("%s %s (Error %d)", $message, "You may also try a different credit card or pay with PayPal.", $code);

		return $message;
	}
}
