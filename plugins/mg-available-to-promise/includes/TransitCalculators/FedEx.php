<?php

namespace MG_ATP\TransitCalculators;

use FedEx\ValidationAvailabilityAndCommitmentService\ComplexType\ServiceAvailabilityRequest;
use FedEx\ValidationAvailabilityAndCommitmentService\Request;
use MG_ATP\Core\Admin;

/**
 * Class FedEx
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package MG_ATP\TransitCalculators
 * @author Clifton Griffin <clif@objectiv.co>
 */

class FedEx extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function load( $key, $password, $account_number, $meter_number ) {
		$service_availability_request = new ServiceAvailabilityRequest();

		$service_availability_request->WebAuthenticationDetail->UserCredential->Key      = $key; // phpcs:ignore
		$service_availability_request->WebAuthenticationDetail->UserCredential->Password = $password; // phpcs:ignore
		$service_availability_request->ClientDetail->AccountNumber                       = $account_number; // phpcs:ignore
		$service_availability_request->ClientDetail->MeterNumber                         = $meter_number; // phpcs:ignore
		$service_availability_request->Version->ServiceId                                = 'vacs'; // phpcs:ignore
		$service_availability_request->Version->Major                                    = 8; // phpcs:ignore
		$service_availability_request->Version->Intermediate                             = 0; // phpcs:ignore
		$service_availability_request->Version->Minor                                    = 0; // phpcs:ignore

		$this->set_api( $service_availability_request );

		return $this;
	}

	/**
	 * @param \DateTime $pickup_date
	 *
	 * @param $options[]
	 *
	 * @return array
	 */
	public function get_transit_times_per_service( $pickup_date, $options ) {
		$return = array();

		$this->get_api()->Origin->PostalCode       = $options['to_postcode'];
		$this->get_api()->Origin->CountryCode      = $options['to_country'];
		$this->get_api()->Destination->PostalCode  = $options['from_postcode'];
		$this->get_api()->Destination->CountryCode = $options['from_country'];
		$this->get_api()->ShipDate                 = $pickup_date->format( 'Y-m-d' );

		$request = new Request();
		$request->getSoapClient()->__setLocation( Request::PRODUCTION_URL );

		try {
			$service_availability_reply = $request->getServiceAvailabilityReply( $this->get_api(), true );

			foreach ( $service_availability_reply->Options as $option ) {
				if ( ! empty( $option->DeliveryDate ) ) {
					$return[ $option->Service ] = new \DateTime( $option->DeliveryDate );
					// We aren't setting the time zone here because the date is local to the delivery
				} elseif ( ! empty( $option->TransitTime ) ) {
					$delivery_date = $pickup_date;
					$delivery_date->modify( $this->map_transit_time( $option->TransitTime ) );
					$return[ $option->Service ] = $delivery_date;
				}
			}
		} catch ( \Exception $e ) {
			error_log( 'MG Available to Promise FedEx Error: ' . $e->getMessage() . $request->getSoapClient()->__getLastResponse() ); // phpcs:ignore
		}

		return $return;
	}

	public function map_transit_time( $transit_time ) {
		$map = array(
			'ONE_DAY'        => '+1 day',
			'TWO_DAYS'       => '+2 days',
			'THREE_DAYS'     => '+3 days',
			'FOUR_DAYS'      => '+4 days',
			'FIVE_DAYS'      => '+5 days',
			'SIX_DAYS'       => '+6 days',
			'SEVEN_DAYS'     => '+7 days',
			'EIGHT_DAYS'     => '+8 days',
			'NINE_DAYS'      => '+9 days',
			'TEN_DAYS'       => '+10 days',
			'ELEVEN_DAYS'    => '+11 days',
			'TWELVE_DAYS'    => '+12 days',
			'THIRTEEN_DAYS'  => '+13 days',
			'FOURTEEN_DAYS'  => '+14 days',
			'FIFTEEN_DAYS'   => '+15 days',
			'SIXTEEN_DAYS'   => '+16 days',
			'SEVENTEEN_DAYS' => '+17 days',
			'EIGHTEEN_DAYS'  => '+18 days',
			'NINETEEN_DAYS'  => '+19 days',
			'TWENTY_DAYS'    => '+20 days',
		);

		return isset( $map[ $transit_time ] ) ? $map[ $transit_time ] : $transit_time;
	}

	/**
	 * @param Admin $admin
	 */
	function settings( $admin ) {
		?>
        <div style="<?php if ( $admin->plugin_instance->get_settings_manager()->get_setting( 'carrier' ) !== 'fedex' ) { echo 'display:none;'; } // phpcs:ignore ?>">
			<h3>FedEx Settings</h3>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row" valign="top">Key</th>
					<td>
						<label>
							<input type="text" name="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_field_name( 'fedex_key' ) ); ?>" value="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_setting( 'fedex_key' ) ); ?>" /><br />
							Key
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Password</th>
					<td>
						<label>
							<input type="text" name="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_field_name( 'fedex_password' ) ); ?>" value="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_setting( 'fedex_password' ) ); ?>" /><br />
							Password
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Account Number</th>
					<td>
						<label>
							<input type="text" name="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_field_name( 'fedex_account_number' ) ); ?>" value="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_setting( 'fedex_account_number' ) ); ?>" /><br />
							Account Number
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Meter Number</th>
					<td>
						<label>
							<input type="text" name="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_field_name( 'fedex_meter_number' ) ); ?>" value="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_setting( 'fedex_meter_number' ) ); ?>" /><br />
							Meter Number
						</label>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
}
