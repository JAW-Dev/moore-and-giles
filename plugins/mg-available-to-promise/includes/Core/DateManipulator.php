<?php

namespace MG_ATP\Core;

use MG_ATP\Main;
use MG_ATP\TransitCalculators\Base;
use MG_ATP\TransitCalculators\UPS;

/**
 * Class Admin
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package MG_ATP\Core
 * @author Clifton Griffin <clif@objectiv.co>
 */

class DateManipulator {
	private $_service_code;
	private $_carrier;
	/**
	 * @var \DateTimeZone
	 */
	private $timezone;

	public function __construct( $service_code, $carrier = false ) {
		/** @var Admin $mg_atp_admin */
		global $mg_atp_admin;

		if ( ! $carrier ) {
			$carrier = $mg_atp_admin->plugin_instance->get_settings_manager()->get_setting( 'carrier' );
		}

		$this->set_service_code( $service_code );
		$this->set_carrier( $carrier );
		$this->timezone = new \DateTimeZone( get_option( 'timezone_string' ) );
	}

	/**
	 * @return mixed
	 */
	public function get_service_code() {
		return $this->_service_code;
	}

	/**
	 * @param mixed $service_code
	 */
	public function set_service_code( $service_code ) {
		$this->_service_code = $service_code;
	}

	/**
	 * @return mixed
	 */
	public function get_carrier() {
		return strtolower( $this->_carrier );
	}

	/**
	 * @param mixed $carrier
	 */
	public function set_carrier( $carrier ) {
		$this->_carrier = $carrier;
	}

	/**
	 * @param \DateTime|bool $assumed_date
	 *
	 * @return \DateTime
	 * @throws \Exception
	 */
	function get_pickup_date( $assumed_date = false ) {
		if ( false === $assumed_date ) {
			$assumed_date = $this->get_latest_backorder_date_from_cart();

			if ( false === $assumed_date ) {
				$assumed_date = new \DateTime( 'now', $this->timezone );
			}
		}

		$today = new \DateTime( 'now', $this->timezone );
		$date  = $this->recursively_calculate_pickup_date( $assumed_date );

		// If time is after cutoff, set to midnight the next day
		if ( $date->format( 'Y-m-d' ) === $today->format( 'Y-m-d' ) && $date->format( 'H' ) >= 11 ) {
			$date->setTime( 0, 0, 0 );
			$date->modify( '+1 day' );
		}

		$date = apply_filters( 'mg_atp_pickup_date', $date );

		return $date;
	}

	/**
	 * @return bool|\DateTime
	 * @throws \Exception
	 */
	function get_latest_backorder_date_from_cart() {
		$date = new \DateTime( 'now', $this->timezone );
		$items_are_backordered = false;

		foreach ( \WC()->cart->get_cart() as $cart_item ) {
			$cart_item_data = $cart_item['data']->get_data();
			$stock_status   = $cart_item_data['stock_status'];

			if ( 'onbackorder' === $stock_status ) {
				$items_are_backordered = true;
				$process_to   = $cart_item['data']->get_meta( 'processing_time__to', true );
				$release_date = $cart_item['data']->get_meta( 'release_date', true );
				$process_num  = ! empty( $process_to ) ? preg_replace( '/[^0-9]/', '', $process_to ) : 0;

				if( $release_date && "Array" !== $release_date ) {
					$new_date = new \DateTime( $release_date, $this->timezone );
				} else if ( $process_num == 1 ) {
					$unit = 'day';

					if ( strpos( $process_to, 'w' ) !== false ) {
						$unit = 'week';
					}

					if ( strpos( $process_to, 'm' ) !== false ) {
						$unit = 'month';
					}

					$new_date = new \DateTime( 'now', $this->timezone );
					$new_date->modify("+{$process_num} {$unit}");
				} else if ( $process_to ) {
					$unit = 'days';

					if ( strpos( $process_to, 'w' ) !== false ) {
						$unit = 'weeks';
					}

					if ( strpos( $process_to, 'm' ) !== false ) {
						$unit = 'months';
					}

					$new_date = new \DateTime( 'now', $this->timezone );
					$new_date->modify("+{$process_num} {$unit}");
				}

				if ( ! empty( $new_date ) && $new_date->getTimestamp() > $date->getTimestamp() ) {
					$date = $new_date;
				}
			}
		}

		return $items_are_backordered ? $date : false;
	}

	/**
	 * @param \DateTime|bool $pickup_date
	 *
	 * @return array
	 * @throws \Exception
	 */
	function get_delivery_dates( $pickup_date = false ) {
		if ( false === $pickup_date ) {
			$pickup_date = $this->get_pickup_date();
		}

		$Main = Main::instance(); // phpcs:ignore

		$transit_manager = '';

		if ( $this->get_carrier() === 'ups' ) {
			/** @var Base $transit_manager */
			$transit_manager = $Main->carriers[ $this->get_carrier() ]->load( $Main->get_settings_manager()->get_setting( 'ups_license' ), $Main->get_settings_manager()->get_setting( 'ups_user_id' ), $Main->get_settings_manager()->get_setting( 'ups_password' ) ); // phpcs:ignore
		} elseif ( $this->get_carrier() === 'fedex' ) {
			/** @var Base $transit_manager */
			$transit_manager = $Main->carriers[ $this->get_carrier() ]->load( $Main->get_settings_manager()->get_setting( 'fedex_key' ), $Main->get_settings_manager()->get_setting( 'fedex_password' ), $Main->get_settings_manager()->get_setting( 'fedex_account_number' ), $Main->get_settings_manager()->get_setting( 'fedex_meter_number' ) ); // phpcs:ignore
		}

		$options = apply_filters( 'mg_get_delivery_dates_order_details', array() );

		$hash = md5( json_encode( $options ) . $this->get_carrier() . $pickup_date->getTimestamp() ); // phpcs:ignore

		if ( false === ( $transit_times = get_transient( 'mg_atp_transit_times_' . $hash ) ) || empty( get_transient( 'mg_atp_transit_times_' . $hash ) ) ) { //phpcs:ignore
			if ( method_exists( $transit_manager, 'get_transit_times_per_service' ) ) {
				$transit_times = $transit_manager->get_transit_times_per_service( $pickup_date, $options );

				// Cache for one day.
				set_transient( 'mg_atp_transit_times_' . $hash, $transit_times, 60 * 60 * 24 );
			} else {
				// error_log( print_r( 'get_transit_times_per_service does not exist', true ) ); // phpcs:ignore
				// error_log( print_r( $transit_manager, true ) ); // phpcs:ignore
			}
		}

		return $transit_times;
	}

	/**
	 * @param \DateTime $date
	 *
	 * @return \DateTime
	 * @throws \Exception
	 */
	function recursively_calculate_pickup_date( $date ) {
		$blackout_date = false;

		if ( $this->is_excluded_date( $date ) ) {
			$blackout_date = true;
		} elseif ( $this->is_holiday( $date ) ) {
			$blackout_date = true;
		} elseif ( $this->is_saturday_or_sunday( $date ) ) {
			$blackout_date = true;
		}

		if ( $blackout_date ) {
			return $this->recursively_calculate_pickup_date( $date->modify( '+1 day' ) );
		}

		return $date;
	}

	/**
	 * @param \DateTime $date
	 *
	 * @return bool
	 * @throws \Exception
	 */
	function is_excluded_date( $date ) {
		$excluded_dates = get_option( '_mg_atp_settings', array() );

		foreach ( $excluded_dates as $excluded_date ) {
			$from = new \DateTime( '@' . strtotime( $excluded_date['from_date'] ), $this->timezone );
			$to   = ! empty( $excluded_date['to_date'] ) ? new \DateTime( '@' . strtotime( $excluded_date['to_date'] ), $this->timezone ) : false;

			if ( ! $to && $date->getTimestamp() >= $from->getTimestamp() && $date->getTimestamp() <= $from->modify( '+1 day' )->modify( '-1 second' )->getTimestamp() ) {
				// If the following conditions are true:
				// - To is NOT set
				// - Date is after from timestamp
				// - Date is before from timestamp at 11:59:59

				return true;
			} elseif ( false !== $to && $date->getTimestamp() >= $from->getTimestamp() && $date->getTimestamp() < $to->modify( '+1 day' )->modify( '-1 second' )->getTimestamp() ) {
				// If the following conditions are true:
				// - To is set
				// - Date is after from timestamp
				// - Date is before to timestamp at 11:59:59

				return true;
			}
		}

		return false;
	}

	/**
	 * @param \DateTime $date
	 *
	 * @return bool
	 */
	function is_holiday( $date ) {
		$current_year     = date( 'Y' );
		$new_years_day    = date( 'Y-m-d', strtotime( "january {$current_year} first day" ) );
		$easter_day       = date( 'Y-m-d', easter_date( $current_year ) );
		$memorial_day     = date( 'Y-m-d', strtotime( "last Monday of May {$current_year}" ) );
		$independence_day = date( 'Y-m-d', strtotime( "july 4 {$current_year}" ) );
		$labor_day        = date( 'Y-m-d', strtotime( "september {$current_year} first monday" ) );
		$thanksgiving_day = date( 'Y-m-d', strtotime( "november {$current_year} fourth thursday" ) );
		$christmas_day    = date( 'Y-m-d', strtotime( "december 25 {$current_year}" ) );

		$holidays = array(
			$new_years_day,
			$easter_day,
			$memorial_day,
			$independence_day,
			$labor_day,
			$thanksgiving_day,
			$christmas_day,
		);

		return in_array( $date->format( 'Y-m-d' ), $holidays, true );
	}

	/**
	 * @param \DateTime $date
	 *
	 * @return bool
	 */
	function is_saturday_or_sunday( $date ) {
		if ( $date->format( 'w' ) === '0' || $date->format( 'w' ) === '6' ) {
			return true;
		}

		return false;
	}
}
