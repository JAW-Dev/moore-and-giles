<?php
include( plugin_dir_path( __FILE__ ) . 'Httpful/Bootstrap.php' );
include( plugin_dir_path( __FILE__ ) . 'Httpful/Http.php' );
include( plugin_dir_path( __FILE__ ) . 'Httpful/Request.php' );

use \Httpful\Request as Request;

class Iglobal {
	protected $_entryPoint = 'https://api.iglobalstores.com/v1/';
	protected $_store      = null; // default store ID
	protected $_key        = '';

	function __construct( $store, $key ) {
		$this->_store = $store;
		$this->_key   = $key;
	}

	protected function zonos_callApi( $path, $data, $entryPoint = 'https://api.iglobalstores.com/v1/', $headers = array() ) {
		$data = json_encode(
			array_merge(
				$data, array(
					'store'  => $this->_store,
					'secret' => $this->_key,
				)
			)
		);
		//TODO: Logging call for data
		$response = Request::post( $entryPoint . $path )
			->sendsJson()
			->expectsJson()
			->body( $data )
			->send();

		//TODO: Logging call for response
		if ( ! $response->hasErrors() ) {
			return $response->body;
		}
		return false;
	}

	public function zonos_orderNumbers( $sinceOrderId = null, $sinceDate = null, $throughDate = null ) {
		$data = array();
		if ( $sinceOrderId ) {
			$data['sinceOrderId'] = $sinceOrderId;
		} elseif ( $sinceDate ) {
			$data['sinceDate'] = $sinceDate;
			if ( $throughDate ) {
				$data['throughDate'] = $throughDate;
			}
		} else {
			throw new Exception( 'sinceOrderId or sinceDate is required' );
		}
		return $this->zonos_callApi( 'orderNumbers', $data );
	}

	public function zonos_orderDetails( $orderId = null, $referenceId = null ) {
		if ( $orderId ) {
			$data = array( 'orderId' => $orderId );
		} elseif ( $referenceId ) {
			$data = array( 'referenceId' => $referenceId );
		} else {
			throw new Exception( 'orderId or referenceId is required' );
		}
		return $this->zonos_callApi( 'orderDetail', $data, $entryPoint = 'https://api.iglobalstores.com/v2/' );
	}

	public function zonos_updateMerchantOrderId( $orderId, $merchantOrderId ) {
		$data = array(
			'orderId'         => $orderId,
			'merchantOrderId' => $merchantOrderId,
		);
		return $this->zonos_callApi( 'updateMerchantOrderId', $data );
	}

	public function zonos_updateVendorOrderStatus( $orderId, $orderStatus ) {
		$data = array(
			'orderId'     => $orderId,
			'orderStatus' => $orderStatus,
		);
		return $this->zonos_callApi( 'updateVendorOrderStatus', $data );
	}

	public function zonos_createTempCart( array $data ) {
		$data = json_encode(
			array_merge(
				$data, array(
					'storeId' => $this->_store,
					'secret'  => $this->_key,
				)
			)
		);
		//TODO: Logging call for data
		$response = Request::post( $this->_entryPoint . 'createTempCart' )
			->sendsJson()
			->expectsJson()
			->body( $data )
			->send();

		//TODO: Logging call for response
		if ( ! $response->hasErrors() ) {
			return $response->body;
		}
		return false;
	}
}
