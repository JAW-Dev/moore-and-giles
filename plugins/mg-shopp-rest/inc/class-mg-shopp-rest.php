<?php

class MG_Shopp_Rest {

	/**
	 * Construct
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Create endpoint by registering the route
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	function register_route() {
		register_rest_route(
			'mgshopprest/v1', 'mgshopprest', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'handle_get_request' ),
			)
		);
	}

	/**
	 * Handles GET requests
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $request The request.
	 * @return void
	 */
	function handle_get_request( $request ) {
		$is_valid = $this->is_valid_http_request( $request );

		if ( $is_valid ) {
			$this->process_swatch_request( $request );
		} else {
			die;
		}
	}

	/**
	 * Validate Request
	 *
	 * @param array $request The request.
	 * @return bool
	 */
	function is_valid_http_request( $request ) {
		$real_secret = 'Eao2HnLbFDAZtJwHbKG82qarqns2OOhty849GnDNpApFvqZmevc9wXnUxkZyud01';
		$headers     = $request->get_headers();
		$sent_secret = isset( $headers['secret'] ) ? $headers['secret'][0] : false;

		return $real_secret === $sent_secret;
	}

	/**
	 * Process a Swatch Request
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $request The request.
	 * @return void
	 */
	function process_swatch_request( $request ) {
		$params   = $request->get_body_params();
		$is_valid = $this->is_valid_swatch_request( $params );

		if ( $is_valid ) {
			$existing_customer = shopp_customer( $params['email'], 'email' );
			$customer          = $existing_customer ? $existing_customer : $this->create_customer( $params );
			$this->create_shopp_order( $params, $customer );
		}
	}

	/**
	 * Swatch Request Has Sufficient Info
	 *
	 * Make sure the incoming swatch request has all the info needed to successfully create an order.
	 *
	 * @param array $params The details of the request.
	 * @return bool
	 */
	function is_valid_swatch_request( $params ) {
		$conditions = array(
			! empty( $params['leathers'] ),
			! empty( $params['first_name'] ),
			! empty( $params['last_name'] ),
			! empty( $params['email'] ) && is_email( $params['email'] ),
			! empty( $params['phone'] ),
			! empty( $params['address_line_1'] ),
			! empty( $params['address_city'] ),
			! empty( $params['address_state'] ),
			! empty( $params['address_zip'] ),
			! empty( $params['address_country'] ),
		);

		return false === array_search( false, $conditions );
	}

	/**
	 * Create Shopp Customer
	 *
	 * @param array $params request options.
	 * @return int Shopp Customer ID.
	 */
	function create_customer( $params ) {
		$options = array(
			'wpuser'    => false,
			'firstname' => $params['first_name'],
			'lastname'  => $params['last_name'],
			'email'     => $params['email'],
			'phone'     => $params['phone'],
			'marketing' => 'no',
			'type'      => 'Guest',
			'saddress'  => $params['address_line_1'],
			'sxaddress' => $params['address_line_2'],
			'scity'     => $params['address_city'],
			'sstate'    => $params['address_state'],
			'scountry'  => $params['address_country'],
			'spostcode' => $params['address_zip'],
			'baddress'  => $params['address_line_1'],
			'bxaddress' => $params['address_line_2'],
			'bcity'     => $params['address_city'],
			'bstate'    => $params['address_state'],
			'bcountry'  => $params['address_country'],
			'bpostcode' => $params['address_zip'],
		);

		$new_customer_id = shopp_add_customer( $options );

		return shopp_customer( $new_customer_id );
	}

	/**
	 * Create an order in Shopp
	 *
	 * @param array  $params order details.
	 * @param object $customer ShoppCustomer.
	 * @return bool|object false or the Purchase object.
	 */
	function create_shopp_order( $params, $customer ) {
		ShoppOrder()->Cart->clear();

		foreach ( $params['leathers'] as $leather ) {
			$item           = new ShoppCartItem();
			$item->name     = $leather;
			$item->quantity = 1;
			$item->shipped  = true;
			$item->type     = 'Shipped';

			ShoppOrder()->Cart->add( $item->fingerprint(), $item );
		}

		ShoppOrder()->firstname          = $params['first_name'];
		ShoppOrder()->lastname           = $params['last_name'];
		ShoppOrder()->shipname           = "{$params['first_name']} {$params['last_name']}";
		ShoppOrder()->Shipping->name     = "{$params['first_name']} {$params['last_name']}";
		ShoppOrder()->Shipping->address  = $params['address_line_1'];
		ShoppOrder()->Shipping->xaddress = $params['address_line_2'];
		ShoppOrder()->Shipping->city     = $params['address_city'];
		ShoppOrder()->Shipping->state    = $params['address_state'];
		ShoppOrder()->Shipping->country  = $params['address_country'];
		ShoppOrder()->Shipping->postcode = $params['address_zip'];
		ShoppOrder()->Billing->name      = "{$params['first_name']} {$params['last_name']}";
		ShoppOrder()->Billing->address   = $params['address_line_1'];
		ShoppOrder()->Billing->xaddress  = $params['address_line_2'];
		ShoppOrder()->Billing->city      = $params['address_city'];
		ShoppOrder()->Billing->state     = $params['address_state'];
		ShoppOrder()->Billing->country   = $params['address_country'];
		ShoppOrder()->Billing->postcode  = $params['address_zip'];
		ShoppOrder()->data               = array(
			'Origin Product'        => $params['product_requested_from'],
			'Product Based Request' => true,
		);

		// check customer
		if ( ! $Customer = shopp_customer( (int) $customer->id ) ) {
			shopp_debug( __FUNCTION__ . ' failed: Invalid customer.' );
			return false;
		}

		if ( ! shopp_cart_items_count() ) {
			shopp_debug( __FUNCTION__ . ' failed: No items in cart.' );
			return false;
		}

		$Order                    = ShoppOrder();
		$Order->Customer          = $Customer;
		$Order->Billing->cardtype = 'api';

		shopp_add_order_event(
			false, 'purchase', array(
				'gateway' => 'GatewayFramework',
			)
		);

		shopp_empty_cart();

		$purchase = ShoppPurchase();

		$purchase->save();

		do_action( 'shopp_order_notifications', $purchase );

		return $purchase;
	}
}
