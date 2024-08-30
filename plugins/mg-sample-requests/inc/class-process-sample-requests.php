<?php
class MG_SampleRequests_Process {
	static $action_field_name  = 'sample_request_action';
	static $action_field_value = 'submit_request';

	public function __construct() {
		// On Submit
		add_action( 'shopp_init', array( $this, 'process_sample_request' ), 1 ); // before anything else happens, son
	}

	function process_sample_request() {
		// Make sure we have a validated request
		if ( ! isset( $_POST[ self::$action_field_name ] ) || $_POST[ self::$action_field_name ] !== self::$action_field_value ) {
			return; // do nothing
		}

		// Make sure we have permission
		if ( ! current_user_can( 'mg_impersonate_user' ) ) {
			return; // do nothing
		}

		// Load cart with selected articles
		$this->add_cart_items();

		// Prevent agent info from being overwritten
		add_filter( 'shopp_validate_registration', '__return_false', 100 );
		add_filter( 'shopp_email_exists', '__return_false' );

		// Change Customer Receipt to Merchant Receipt
		add_filter( 'shopp_order_event_emails', array( $this, 'change_customer_receipt_template' ) );

		// Notify receipient that a sample request has been submitted on their behalf
		add_action( 'shopp_order_success', array( $this, 'recipient_order_notification' ), 101 );

		// Set shipoption on purchase record from data
		add_action( 'shopp_order_success', array( $this, 'set_ship_option' ), 100, 2 );

		// Set internal request flag
		add_action( 'shopp_order_success', array( $this, 'set_internal_flag' ) );

		// Create new customer
		add_action( 'shopp_order_success', array( $this, 'create_new_customer' ), 100, 1 );

		// Save existing customer
		add_action( 'shopp_order_success', array( $this, 'save_existing_customer' ), 100, 2 );

		// Reset sesssion
		add_action( 'shopp_order_success', array( $this, 'reset_session' ), 100, 1 );

		add_action( 'shopp_order_success', array( $this, 'extra_notifications' ), 110, 1 );
	}

	function add_cart_items() {
		// Clear cart of existing items
		ShoppOrder()->Cart->clear();

		// process form data
		$articles_lines = $_POST['article_line'];

		foreach ( $articles_lines as $line ) {
			extract( $line );
			list($price,$product) = explode( '|', $price_id );

			$Product = new ShoppProduct( (int) $product );

			ShoppOrder()->Cart->additem( $quantity, $Product, $price, false, array( 'Size' => $size ), array() );
		}

		$custom_article_lines = $_POST['custom_article_line'];

		foreach ( $custom_article_lines as $line ) {
			extract( $line );

			$NewItem           = new ShoppCartItem();
			$NewItem->name     = "$name - $label";
			$NewItem->data     = array( 'Size' => $size );
			$NewItem->quantity = $quantity;
			$NewItem->shipped  = true;
			$NewItem->type     = 'Shipped';

			$id = $NewItem->fingerprint();
			ShoppOrder()->Cart->add( $id, $NewItem );
		}

		$box_lines = $_POST['box_line'];

		foreach ( $box_lines as $line ) {
			extract( $line );

			$NewItem           = new ShoppCartItem();
			$NewItem->name     = "$name";
			$NewItem->quantity = $quantity;
			$NewItem->shipped  = true;
			$NewItem->type     = 'Shipped';

			$id = $NewItem->fingerprint();
			ShoppOrder()->Cart->add( $id, $NewItem );
		}

		$custom_order_line = $_POST['custom_order_line'];

		foreach ( $custom_order_line as $line ) {
			$NewItem           = new ShoppCartItem();
			$NewItem->name     = 'Custom Sample';
			$NewItem->quantity = $line['quantity'];
			$NewItem->shipped  = true;
			$NewItem->type     = 'Shipped';

			$NewItem->data['Pattern']      = $line['pattern'];
			$NewItem->data['Base Leather'] = $line['name'];
			$NewItem->data['Tipping']      = $line['tipping'];

			$id = $NewItem->fingerprint();
			ShoppOrder()->Cart->add( $id, $NewItem );
		}

	}

	function change_customer_receipt_template( $emails ) {
		$emails['customer'][3] = str_ireplace( 'email-order.php', 'email-merchant-order.php', $emails['customer'][3] );

		return $emails;
	}

	function recipient_order_notification( $Purchase ) {

		$emails = $this->emails_array( $Purchase->data['Recipient Email'] );

		foreach ( $emails as $e ) {
			$Purchase->email(
				'',
				$e,
				__( 'A sample request has been submitted on your behalf', 'Shopp' ),
				array( 'email-sample-request-customer.php' )
			);
		}
	}

	function extra_notifications( $Purchase ) {
		if ( isset( $_POST['custom_order_line'] ) ) {
			$Purchase->email(
				'',
				'ProductDevelopment@mooreandgiles.com',
				'Custom Sample Order (Requires Approval)',
				array( 'email-custom-sample-order-approval.php' )
			);
		}
	}

	function set_ship_option( $Purchase ) {
		if ( isset( $Purchase->data['Shipping Method'] ) ) {
			$Purchase->shipoption = $Purchase->data['Shipping Method'];
			$Purchase->save();
		}
	}

	function set_internal_flag( $Purchase ) {
		if ( isset( $Purchase->data['_intended_recipient'] ) ) {
			shopp_set_meta( $Purchase->id, 'purchase', 'internal_sample_request', true );
		}
	}

	function create_new_customer( $Purchase ) {

		if ( isset( $Purchase->data['_intended_recipient'] ) && $Purchase->data['_intended_recipient'] == 'New Customer' ) {

			$name = $Purchase->shipname;

			if ( strpos( $name, ' ' ) !== false ) {
				$name = explode( ' ', $name );

				$firstname = $name[0];
				$lastname  = $name[ count( $name ) - 1 ];
			} else {
				$firstname = $name;
				$lastname  = '';
			}

			$emails = $this->emails_array( $Purchase->data['Recipient Email'] );

			$data = array(
				'wpuser'    => false,
				'firstname' => $firstname,
				'lastname'  => $lastname,
				'email'     => $emails[0],
				'phone'     => $Purchase->data['Shipping Phone'],
				'company'   => $Purchase->data['Recipient Company'],
				'marketing' => 'no',
				'type'      => 'Guest',
				'saddress'  => $Purchase->shipaddress,
				'sxaddress' => $Purchase->shipxaddress,
				'scity'     => $Purchase->shipcity,
				'sstate'    => $Purchase->shipstate,
				'scountry'  => $Purchase->shipcountry,
				'spostcode' => $Purchase->shippostcode,
				'baddress'  => $Purchase->address,
				'bxaddress' => $Purchase->xaddress,
				'bcity'     => $Purchase->city,
				'bstate'    => $Purchase->state,
				'bcountry'  => $Purchase->country,
				'bpostcode' => $Purchase->postcode,
			);

			$customerid = shopp_add_customer( $data );
		}

	}

	function save_existing_customer( $Purchase ) {

		// Existing Customer
		if ( isset( $Purchase->data['_intended_recipient'] ) && $Purchase->data['_intended_recipient'] == 'Existing Customer' ) {
			$Customer = shopp_customer( $Purchase->data['_selected_customer_address'] );

			if ( strpos( $Purchase->shipname, ' ' ) !== false ) {
				$name = explode( ' ', $Purchase->shipname );

				$firstname = $name[0];
				$lastname  = $name[ count( $name ) - 1 ];
			} else {
				$firstname = $name;
				$lastname  = '';
			}

			// Update selected info
			$Customer->firstname = $firstname;
			$Customer->lastname  = $lastname;
			$Customer->phone     = $Purchase->data['Shipping Phone'];
			$Customer->company   = $Purchase->data['Recipient Company'];
			$Customer->save();

			// Update billing address
			$BillingAddress           = new BillingAddress( $Customer->id, 'customer' );
			$BillingAddress->name     = $Purchase->shipname;
			$BillingAddress->address  = $Purchase->address;
			$BillingAddress->xaddress = $Purchase->xaddress;
			$BillingAddress->city     = $Purchase->city;
			$BillingAddress->state    = $Purchase->state;
			$BillingAddress->postcode = $Purchase->postcode;
			$BillingAddress->country  = $Purchase->country;
			$BillingAddress->customer = $Customer->id;
			$BillingAddress->save();

			// Update shipping address
			$ShippingAddress           = new ShippingAddress( $Customer->id, 'customer' );
			$ShippingAddress->name     = $Purchase->shipname;
			$ShippingAddress->address  = $Purchase->shipaddress;
			$ShippingAddress->xaddress = $Purchase->shipxaddress;
			$ShippingAddress->city     = $Purchase->shipcity;
			$ShippingAddress->state    = $Purchase->shipstate;
			$ShippingAddress->postcode = $Purchase->shippostcode;
			$ShippingAddress->country  = $Purchase->shipcountry;
			$ShippingAddress->customer = $Customer->id;
			$ShippingAddress->save();
		}

		// Agent
		if ( isset( $Purchase->data['_intended_recipient'] ) && $Purchase->data['_intended_recipient'] == 'Yourself' ) {
			$Customer = ShoppCustomer();

			// Update billing address
			$BillingAddress           = new BillingAddress( $Customer->id, 'customer' );
			$BillingAddress->name     = $Purchase->shipname;
			$BillingAddress->address  = $Purchase->address;
			$BillingAddress->xaddress = $Purchase->xaddress;
			$BillingAddress->city     = $Purchase->city;
			$BillingAddress->state    = $Purchase->state;
			$BillingAddress->postcode = $Purchase->postcode;
			$BillingAddress->country  = $Purchase->country;
			$BillingAddress->customer = $Customer->id;
			$BillingAddress->save();

			// Update shipping address
			$ShippingAddress           = new ShippingAddress( $Customer->id, 'customer' );
			$ShippingAddress->name     = $Purchase->shipname;
			$ShippingAddress->address  = $Purchase->shipaddress;
			$ShippingAddress->xaddress = $Purchase->shipxaddress;
			$ShippingAddress->city     = $Purchase->shipcity;
			$ShippingAddress->state    = $Purchase->shipstate;
			$ShippingAddress->postcode = $Purchase->shippostcode;
			$ShippingAddress->country  = $Purchase->shipcountry;
			$ShippingAddress->customer = $Customer->id;
			$ShippingAddress->save();
		}
	}

	function emails_array( $email_list ) {
		$recipient_emails = str_replace( ' ', '', $email_list );
		if ( strpos( $recipient_emails, ',' ) !== false ) {
			$recipient_emails = explode( ',', $recipient_emails );
		}

		$emails = (array) $recipient_emails;
		return $emails;
	}

	function reset_session() {
		ShoppShopping()->reset();
	}
}
