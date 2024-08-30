<?php
/**
 * MES SDK.
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

/**
 * MES SDK.
 */
class TpgTransaction { // phpcs:ignore

	/* @var boolean */ // phpcs:ignore
	protected $post = true; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $api_host = 'https://cert.merchante-solutions.com/mes-api/tridentApi'; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $proxy_host = ''; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $profile_id; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $profile_key; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $tran_type = 'A'; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $api_response; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $error_message; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $response_raw; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $response_fields; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $request_fields; // phpcs:ignore

	/* @var string */ // phpcs:ignore
	protected $url; // phpcs:ignore

	/* @var array */ // phpcs:ignore
	protected $request_field_names = array( // phpcs:ignore
		'store_card',
		'avs_data',
		'cardholder_street_address',
		'cardholder_zip',
		'cvv2',
		'transaction_amount',
		'card_number',
		'card_exp_date',
		'transaction_id',
		'card_present',
		'reference_number',
		'merchant_name',
		'merchant_city',
		'merchant_state',
		'merchant_zip',
		'merchant_category_code',
		'merchant_phone',
		'invoice_number',
		'tax_amount',
		'ship_to_zip',
		'moto_ecommerce_ind',
		'industry_code',
		'auth_code',
		'card_id',
		'country_code',
		'fx_amount',
		'fx_rate_id',
		'currency_code',
		'rctl_product_level',
		'echo_customfield',
		'3d_payload',
		'3d_transaction_id',
		'client_reference_number',
		'bml_request',
		'promo_code',
		'order_num',
		'order_desc',
		'amount',
		'ship_amount',
		'ip_address',
		'bill_first_name',
		'bill_middle_name',
		'bill_last_name',
		'bill_addr1',
		'bill_addr2',
		'bill_city',
		'bill_state',
		'bill_zip',
		'bill_phone1',
		'bill_phone2',
		'bill_email',
		'ship_first_name',
		'ship_middle_name',
		'ship_last_name',
		'ship_addr1',
		'ship_addr2',
		'ship_city',
		'ship_state',
		'ship_zip',
		'ship_phone1',
		'ship_phone2',
		'ship_email',
		'auth_response_text',
		'error_code',
		'avs_result',
		'cvv2_result',
		'auth_code',
	);

	/**
	 * The constructor
	 *
	 * @param string $profile_id The profile ID.
	 * @param string $profile_key The profile Key.
	 *
	 * @return void
	 */
	public function __construct( $profile_id = '', $profile_key = '' ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
	}

	/**
	 * TpgTransaction Init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 *
	 * @return void
	 */
	public function TpgTransactionInit( $profile_id = '', $profile_key = '' ) {
		$this->setProfile( $profile_id, $profile_key );
	}

	/**
	 * Execute
	 *
	 * @return void
	 */
	public function execute() {
		if ( $this->isValid() ) {
			$url  = '';
			$url .= 'profile_id=' . $this->profile_id;
			$url .= '&profile_key=' . $this->profile_key;

			$url .= '&transaction_type=' . $this->tran_type;

			foreach ( $this->request_field_names as $fname ) {
				if ( isset( $this->request_fields[ $fname ] ) ) {
					$url .= '&' . $fname . '=' . $this->request_fields[ $fname ];
				}
			}
			$this->url = $url;
			$this->processRequest();
		}
	}

	/**
	 * Get Response Field
	 *
	 * @param string $field_name The fieldname.
	 * @return string
	 */
	public function getResponseField( $field_name ) { // phpcs:ignore
		$ret_val = '';
		if ( isset( $this->response_fields[ $field_name ] ) ) {
			$ret_val = $this->response_fields[ $field_name ];
		}
		return( $ret_val );
	}

	/**
	 * Get Reponse Fields
	 *
	 * @return array
	 */
	public function get_response_fields() {
		$response_fields = array();
		foreach ( $this->request_field_names as $name ) {
			if ( isset( $this->response_fields[ $name ] ) && $this->getResponseField( $name ) ) {
				$response_fields[ $name ] = $this->getResponseField( $name );
			}
		}
		return $response_fields;
	}

	/**
	 * Get Transaction Type
	 *
	 * @return string
	 */
	public function get_trans_type() {
		return $this->tran_type;
	}

	/**
	 * Is Approved
	 *
	 * @return bool
	 */
	public function isApproved() { // phpcs:ignore
		$error_code = $this->getResponseField( 'error_code' );
		$ret_val    = false;
		if ( '000' === $error_code ) {
			$ret_val = true;
		} else if ( '085' === $error_code && 'A' === $this->tran_type ) { // phpcs:ignore
			$ret_val = true;
		}
		return( $ret_val );
	}

	/**
	 * Is Blank
	 *
	 * @param string $value The value.
	 * @return bool
	 */
	public function isBlank( $value ) { // phpcs:ignore
		return( '' === $value );
	}

	/**
	 * Is Valid
	 *
	 * @return bool
	 */
	public function isValid() { // phpcs:ignore
		$ret_val             = true; // Assume true.
		$this->error_message = '';
		if ( $this->isBlank( $this->profile_id ) || $this->isBlank( $this->profile_key ) ) {
			$this->error_message = 'Missing profile data';
		} elseif ( isset( $this->request_fields['transaction_amount'] ) && ! is_numeric( $this->request_fields['transaction_amount'] ) ) {
			$this->error_message = 'Amount must be a number';
		}
		return( $this->isBlank( $this->error_message ) );
	}

	/**
	 * Parse Response
	 *
	 * @param string $response The response.
	 * @return void
	 */
	function parseResponse( $response ) { // phpcs:ignore
		$this->response_raw = $response;
		$response_fields    = explode( '&', $response );

		foreach ( $response_fields as $field ) {
			$name_value                              = explode( '=', $field );
			$this->response_fields[ $name_value[0] ] = $name_value[1];
		}
	}

	/**
	 * Process request
	 *
	 * @return void
	 */
	public function processRequest() { // phpcs:ignore
		$ch = curl_init(); // phpcs:ignore

		if ( $this->post ) {
			curl_setopt( $ch, CURLOPT_POST, true ); // phpcs:ignore
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->url ); // phpcs:ignore
			curl_setopt( $ch, CURLOPT_URL, $this->api_host ); // phpcs:ignore
		} else {
			curl_setopt( $ch, CURLOPT_URL, $url = $this->api_host . '?' . $this->url ); // phpcs:ignore
		}

		curl_setopt( $ch, CURLOPT_FRESH_CONNECT, true ); // phpcs:ignore
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // phpcs:ignore
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false ); // phpcs:ignore
		curl_setopt( $ch, CURLOPT_HEADER, 0 ); // phpcs:ignore

		if ( ! $this->isBlank( $this->proxy_host ) ) {
			curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP ); // phpcs:ignore
			curl_setopt ($ch, CURLOPT_PROXY, $this->proxy_host ); // phpcs:ignore
		}

		curl_setopt( $ch, CURLOPT_TIMEOUT, 120 ); // phpcs:ignore
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // phpcs:ignore

		$this->parseResponse( curl_exec( $ch ) ); // phpcs:ignore
	}

	/**
	 * Undocumented function
	 *
	 * @param string $cardholder_street_addr The address.
	 * @param string $cardholder_zip         The zip.
	 * @return void
	 */
	public function setAvsRequest( $cardholder_street_addr, $cardholder_zip ) { // phpcs:ignore
		$this->setRequestField( 'cardholder_street_address', $cardholder_street_addr );
		$this->setRequestField( 'cardholder_zip', $cardholder_zip );
	}

	/**
	 * Set Host
	 *
	 * @param string $host The host.
	 * @return void
	 */
	public function setHost( $host ) { // phpcs:ignore
		$this->api_host = $host;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @return void
	 */
	public function setProfile( $profile_id, $profile_key ) { // phpcs:ignore
		$this->profile_id  = $profile_id;
		$this->profile_key = $profile_key;
	}

	/**
	 * Set Proxy Host
	 *
	 * @param string $proxy_host The proxy host.
	 * @return void
	 */
	public function setProxyHost( $proxy_host ) { // phpcs:ignore
		$this->proxy_host = $proxy_host;
	}

	/**
	 * Set Request Field
	 *
	 * @param string $field_name  The field name.
	 * @param string $field_value The fields value.
	 * @return void
	 */
	public function setRequestField( $field_name, $field_value ) { // phpcs:ignore
		$this->request_fields[ $field_name ] = rawurlencode( $field_value );
	}

	/**
	 * Set Transaction Data
	 *
	 * @param string $card_number The card number.
	 * @param string $exp_date    The expiration date.
	 * @param float  $tran_amount The transaction amount.
	 * @return void
	 */
	public function setTransactionData( $card_number, $exp_date, $tran_amount = 0.0 ) { // phpcs:ignore
		$this->request_fields['card_number']        = $card_number;
		$this->request_fields['card_exp_date']      = $exp_date;
		$this->request_fields['transaction_amount'] = $tran_amount;
	}

	/**
	 * Set Post
	 *
	 * @param boolean $bool The boolean.
	 * @return void
	 */
	public function setPost( $bool ) { // phpcs:ignore
		$this->post = $bool;
	}

	/**
	 * Set Dynamic Data
	 *
	 * @param string $name  The name.
	 * @param string $city  The city.
	 * @param string $state The state.
	 * @param string $zip   The zip.
	 * @param string $mcc   The mmc.
	 * @param string $phone The phone number.
	 * @return void
	 */
	public function setDynamicData( $name, $city, $state, $zip, $mcc, $phone ) { // phpcs:ignore
		$this->request_fields['merchant_name']          = $name;
		$this->request_fields['merchant_city']          = $city;
		$this->request_fields['merchant_state']         = $state;
		$this->request_fields['merchant_zip']           = $zip;
		$this->request_fields['merchant_category_code'] = $mcc;
		$this->request_fields['merchant_phone']         = $phone;
	}
}

/**
 * Pre Authorization
 */
class TpgPreAuth extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key he profile Key.
	 * @return void
	 */
	public function __construct( $profile_id = '', $profile_key = '' ) {
		$this->TpgPreAuthInit( $profile_id, $profile_key );
	}

	/**
	 * Pre Authorization init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key he profile Key.
	 * @return void
	 */
	public function TpgPreAuthInit( $profile_id = '', $profile_key = '' ) { // phpcs:ignore
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->tran_type = 'P'; // pre-auth.
	}

	/**
	 * Set Store Data
	 *
	 * @param int $card_id The card ID.
	 * @param int $amount  The amount.
	 * @return void
	 */
	public function setStoredData( $card_id, $amount ) { // phpcs:ignore
		$this->request_fields['card_id']            = $card_id;
		$this->request_fields['transaction_amount'] = $amount;
	}

	/**
	 * Set FX Data
	 *
	 * @param int    $amt  The fixed amount.
	 * @param int    $rid  The fixed rate.
	 * @param string $curr The currency code.
	 * @return void
	 */
	public function setFXData( $amt, $rid, $curr ) {
		$this->request_fields['fx_amount']     = $amt;
		$this->request_fields['fx_rate_id']    = $rid;
		$this->request_fields['currency_code'] = $curr;
	}

	/**
	 * Set Ecommerce Ind
	 *
	 * @param string $ind The ind.
	 * @return void
	 */
	public function setEcommInd( $ind ) { // phpcs:ignore
		$this->request_fields['moto_ecommerce_ind'] = $ind;
	}
}

/**
 * Sale
 */
class TpgSale extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @return void
	 */
	public function __construct( $profile_id = '', $profile_key = '' ) {
		$this->TpgSaleInit( $profile_id, $profile_key );
	}

	/**
	 * Sale Init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @return void
	 */
	public function TpgSaleInit( $profile_id, $profile_key ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->tran_type = 'D';
	}

	/**
	 * Set Stores Data
	 *
	 * @param int $card_id The card ID.
	 * @param int $amount  The amount.
	 * @return void
	 */
	public function setStoredData( $card_id, $amount ) {
		$this->request_fields['card_id']            = $card_id;
		$this->request_fields['transaction_amount'] = $amount;
	}

	/**
	 * Set FX Data
	 *
	 * @param int    $amt  The fixed amount.
	 * @param int    $rid  The fixed rate.
	 * @param string $curr The currency code.
	 * @return void
	 */
	public function setFXData( $amt, $rid, $curr ) {
		$this->request_fields['fx_amount']     = $amt;
		$this->request_fields['fx_rate_id']    = $rid;
		$this->request_fields['currency_code'] = $curr;
	}

	/**
	 * Set Ecommerce Ind
	 *
	 * @param string $ind The ind.
	 * @return void
	 */
	public function setEcommInd( $ind ) { // phpcs:ignore
		$this->request_fields['moto_ecommerce_ind'] = $ind;
	}
}

/**
 * Credit
 */
class TpgCredit extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor.
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @return void
	 */
	public function __construct( $profile_id, $profile_key ) {
		$this->TpgCreditInit( $profile_id, $profile_key );
	}

	/**
	 * Credit init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @return void
	 */
	public function TpgCreditInit( $profile_id, $profile_key ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->tran_type = 'C';
	}

	/**
	 * Set Stores Data
	 *
	 * @param int $card_id The card ID.
	 * @param int $amount  The amount.
	 * @return void
	 */
	public function setStoredData( $card_id, $amount ) {
		$this->request_fields['card_id']            = $card_id;
		$this->request_fields['transaction_amount'] = $amount;
	}
}

/**
 * Settle
 */
class TpgSettle extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor.
	 *
	 * @param string $profile_id    The profile ID.
	 * @param string $profile_key   The profile Key.
	 * @param int    $tran_id       The transaction ID.
	 * @param int    $settle_amount The settle ammount.
	 * @return void
	 */
	public function __construct( $profile_id, $profile_key, $tran_id, $settle_amount = 0 ) {
		$this->TpgSettleInit( $profile_id, $profile_key, $tran_id, $settle_amount );
	}

	/**
	 * Settle init
	 *
	 * @param string $profile_id    The profile ID.
	 * @param string $profile_key   The profile Key.
	 * @param int    $tran_id       The transaction ID.
	 * @param int    $settle_amount The settle ammount.
	 * @return void
	 */
	public function TpgSettleInit( $profile_id, $profile_key, $tran_id, $settle_amount ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->request_fields['transaction_id']     = $tran_id;
		$this->request_fields['transaction_amount'] = $settle_amount;
		$this->tran_type                            = 'S';
	}

	/**
	 * Set Settle Amount
	 *
	 * @param int $settle_amount The settle amount.
	 * @return void
	 */
	public function setSettlementAmount( $settle_amount ) {
		$this->request_fields['transaction_amount'] = $settle_amount;
	}
}

/**
 * Refund
 */
class TpgRefund extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor.
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $tran_id     The transaction ID.
	 * @return void
	 */
	public function __construct( $profile_id, $profile_key, $tran_id ) {
		$this->TpgRefundInit( $profile_id, $profile_key, $tran_id );
	}

	/**
	 * Refund Init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $tran_id     The transaction ID.
	 * @return void
	 */
	public function TpgRefundInit( $profile_id, $profile_key, $tran_id ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->request_fields['transaction_id'] = $tran_id;
		$this->tran_type                        = 'U';
	}

	/**
	 * Set Stores Data
	 *
	 * @param int $card_id The card ID.
	 * @param int $amount  The amount.
	 * @return void
	 */
	public function setStoredData( $card_id, $amount ) {
		$this->request_fields['card_id']            = $card_id;
		$this->request_fields['transaction_amount'] = $amount;
	}
}

/**
 * Void
 */
class TpgVoid extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor.
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $tran_id     The transaction ID.
	 * @return void
	 */
	public function __construct( $profile_id, $profile_key, $tran_id ) {
		$this->TpgVoidInit( $profile_id, $profile_key, $tran_id );
	}

	/**
	 * Void init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $tran_id     The transaction ID.
	 * @return void
	 */
	public function TpgVoidInit( $profile_id, $profile_key, $tran_id ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->request_fields['transaction_id'] = $tran_id;
		$this->tran_type                        = 'V';
	}

	/**
	 * Set Stores Data
	 *
	 * @param int $card_id The card ID.
	 * @param int $amount  The amount.
	 * @return void
	 */
	public function setStoredData( $card_id, $amount ) {
		$this->request_fields['card_id']            = $card_id;
		$this->request_fields['transaction_amount'] = $amount;
	}
}

/**
 * Offline
 */
class TpgOffline extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor.
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $auth_code   The authorization code.
	 * @return void
	 */
	public function __construct( $profile_id, $profile_key, $auth_code ) {
		$this->TpgOfflineInit( $profile_id, $profile_key, $auth_code );
	}

	/**
	 * Offline init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $auth_code   The authorization code.
	 * @return void
	 */
	public function TpgOfflineInit( $profile_id, $profile_key, $auth_code ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->request_fields['auth_code'] = $auth_code;
		$this->tran_type                   = 'O';
	}

	/**
	 * Set Stores Data
	 *
	 * @param int $card_id The card ID.
	 * @param int $amount  The amount.
	 * @return void
	 */
	public function setStoredData( $card_id, $amount ) {
		$this->request_fields['card_id']            = $card_id;
		$this->request_fields['transaction_amount'] = $amount;
	}
}

/**
 * Store Data
 */
class TpgStoreData extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor.
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @return void
	 */
	public function __construct( $profile_id, $profile_key ) {
		$this->TpgStoreDataInit( $profile_id, $profile_key );
	}

	/**
	 * Store Data init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @return void
	 */
	public function TpgStoreDataInit( $profile_id, $profile_key ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->tran_type = 'T';
	}
}

/**
 * Remove Data
 */
class TpgRemoveData extends TpgTransaction { // phpcs:ignore

	/**
	 * The constructor.
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $card_id     The card ID.
	 * @return void
	 */
	public function __construct( $profile_id, $profile_key, $card_id ) {
		$this->TpgRemoveDataInit( $profile_id, $profile_key, $card_id );
	}

	/**
	 * Remove Data init
	 *
	 * @param string $profile_id  The profile ID.
	 * @param string $profile_key The profile Key.
	 * @param int    $card_id     The card ID.
	 * @return void
	 */
	public function TpgRemoveDataInit( $profile_id, $profile_key, $card_id ) {
		$this->TpgTransactionInit( $profile_id, $profile_key );
		$this->request_fields['card_id'] = $card_id;
		$this->tran_type                 = 'X';
	}
}
