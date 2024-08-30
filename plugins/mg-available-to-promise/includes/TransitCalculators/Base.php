<?php

namespace MG_ATP\TransitCalculators;

/**
 * Class Base
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package MG_ATP\TransitCalculators
 * @author Clifton Griffin <clif@objectiv.co>
 */

abstract class Base {
	private $_api;
	/**
	 * @var \DateTimeZone
	 */
	private $timezone;

	/**
	 * @return mixed
	 */
	public function get_api() {
		return $this->_api;
	}

	/**
	 * @param mixed $api
	 */
	public function set_api( $api ) {
		$this->_api = $api;
	}

	public function __construct() {
		$this->timezone = new \DateTimeZone( get_option( 'timezone_string' ) );

		add_filter( 'mg_atp_carriers', array( $this, 'add_carrier' ) );
		add_action( 'mg_atp_carrier_settings', array( $this, 'settings' ), 10, 1 );
	}

	/**
	 * @param \DateTime $pickup_date
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_transit_times_per_service( $pickup_date, $options ) {
		return array();
	}

	function add_carrier( $carriers ) {
		$class_path = explode( '\\', get_called_class() );
		$carriers[] = end( $class_path );

		return $carriers;
	}

	/**
	 * @return \DateTimeZone
	 */
	public function get_timezone() {
		return $this->timezone;
	}

	/**
	 * @param \DateTimeZone $timezone
	 */
	public function set_timezone( $timezone ) {
		$this->timezone = $timezone;
	}
}
