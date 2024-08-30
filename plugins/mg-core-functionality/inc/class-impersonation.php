<?php
class MG_Impersonation {
	public function __construct() {
		if ( ! current_user_can( 'mg_impersonate_user' ) ) {
			return;
		}

		// Allow duplicate email addresses
		add_filter( 'shopp_email_exists', '__return_false' );

		// Customer Lookup
		add_action( 'wp_ajax_impersonate_customer', array( $this, 'load_customer' ) );

		// Attach the order
		add_action( 'shopp_process_checkout', array( $this, 'attach_impersonation_customer' ) );

		// Don't process registration during impersonation
		add_filter( 'shopp_validate_registration', '__return_false' );

		// Update Customer Data / Addresses
		add_action( 'shopp_order_success', array( $this, 'update_customer' ), 1 );

		// Re-load Agent Customer
		add_action( 'shopp_order_success', array( $this, 'reload_agent' ), 100 );
	}

	/**
	 * Attach the correct customer to this order
	 * Without this, the order will attach to agent customer and hijack all information
	 * Note: This only works because the re-ordering of the data hook in class-customer-matching.php
	 */
	function attach_impersonation_customer() {
		if ( isset( ShoppOrder()->data['Impersonated Customer'] ) && is_numeric( ShoppOrder()->data['Impersonated Customer'] ) ) {
			ShoppOrder()->Customer->id = ShoppOrder()->data['Impersonated Customer'];
		}
	}

	/**
	 * Load customer (or create new one)
	 * Return the customer data
	 */
	function load_customer() {
		get_currentuserinfo();

		$Order = ShoppOrder();

		if ( $_REQUEST['customer'] != 'new' ) {
			$customer_id = absint( $_REQUEST['customer'] );

			$Order->Customer->load( $customer_id );
			unset( $Order->Customer->password );

			$Order->Billing->load( $customer_id, 'customer' );
			$Order->Billing->card        = '';
			$Order->Billing->cardexpires = '';
			$Order->Billing->cardholder  = '';
			$Order->Billing->cardtype    = '';
			$Order->Shipping->load( $customer_id, 'customer' );
			if ( empty( $Order->Shipping->id ) ) {
				$Order->Shipping->copydata( $Order->Billing );
			}
		} else {
			$NewCustomer = new ShoppCustomer();
			$NewCustomer->save();

			$Order->Customer->load( $NewCustomer->id );
			$Order->Customer->wplogin = 'guest';

			$Order->Billing  = new BillingAddress();
			$Order->Shipping = new ShippingAddress();
			$customer_id     = $Order->Customer->id;
		}

		ShoppOrder()->data['Impersonated Customer'] = $customer_id;

		$return                    = array();
		$return['customer_id']     = $customer_id;
		$return['firstname']       = $Order->Customer->firstname;
		$return['lastname']        = $Order->Customer->lastname;
		$return['email']           = $Order->Customer->email;
		$return['company']         = $Order->Customer->company;
		$return['phone']           = $Order->Customer->phone;
		$return['billingname']     = $Order->Billing->name;
		$return['billingaddress']  = $Order->Billing->address;
		$return['billingxaddress'] = $Order->Billing->xaddress;
		$return['billingcity']     = $Order->Billing->city;
		$return['billingstate']    = $Order->Billing->state;
		$return['billingcountry']  = $Order->Billing->country;
		$return['billingpostcode'] = $Order->Billing->postcode;

		$return['shippingname']     = $Order->Shipping->name;
		$return['shippingaddress']  = $Order->Shipping->address;
		$return['shippingxaddress'] = $Order->Shipping->xaddress;
		$return['shippingcity']     = $Order->Shipping->city;
		$return['shippingstate']    = $Order->Shipping->state;
		$return['shippingcountry']  = $Order->Shipping->country;
		$return['shippingpostcode'] = $Order->Shipping->postcode;

		$return = apply_filters( 'mg_impersonate_customer_return', $return );

		echo json_encode( $return );
		exit();
	}

	function update_customer( $Purchase ) {
		ShoppOrder()->Customer->save();

		// Update Addresses
		$addresses = array( 'Billing', 'Shipping' );
		foreach ( $addresses as $Address ) {
			if ( empty( ShoppOrder()->$Address->address ) ) {
				continue;
			}
			$Address = ShoppOrder()->$Address;

			$NewAddress = shopp_address( ShoppOrder()->Customer->id, $Address->type );
			if ( ! $NewAddress ) {
				$NewAddress           = new ShoppAddress();
				$NewAddress->customer = ShoppOrder()->Customer->id;
			}

			$NewAddress->type     = $Address->type;
			$NewAddress->name     = $Address->name;
			$NewAddress->address  = $Address->address;
			$NewAddress->xaddress = $Address->xaddress;
			$NewAddress->city     = $Address->city;
			$NewAddress->state    = $Address->state;
			$NewAddress->country  = $Address->country;
			$NewAddress->postcode = $Address->postcode;
			$NewAddress->save();
		}
	}

	function reload_agent( $Purchase ) {
		if ( isset( $Purchase->data['Impersonated Customer'] ) && isset( $Purchase->data['_impersonating_agent'] ) && $Purchase->data['_impersonating_agent'] > 0 ) {
			$user  = wp_get_current_user();
			$Order = ShoppOrder();

			if ( $Account = new ShoppCustomer( $Purchase->data['_impersonating_agent'], 'wpuser' ) ) {

				// Make sure customer didn't pick up any of agent's data
				$customer = shopp_customer( $Purchase->customer );

				if ( $customer->wpuser == $user->ID && $Purchase->data['Impersonated Customer'] != $Account->id ) {
					$customer->wpuser    = null;
					$customer->userlogin = null;
					$customer->save();
				}

				$Order->Customer->load( $Account->id );
				unset( $Order->Customer->password );

				$Order->Billing->load( $Account->id, 'customer' );
				$Order->Billing->card        = '';
				$Order->Billing->cardexpires = '';
				$Order->Billing->cardholder  = '';
				$Order->Billing->cardtype    = '';
				$Order->Shipping->load( $Account->id, 'customer' );
				if ( empty( $Order->Shipping->id ) ) {
					$Order->Shipping->copydata( $Order->Billing );
				}
			}
		}
	}
}
