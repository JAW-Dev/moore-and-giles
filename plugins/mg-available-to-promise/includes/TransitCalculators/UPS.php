<?php

namespace MG_ATP\TransitCalculators;

use MG_ATP\Core\Admin;

/**
 * Class UPS
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package MG_ATP\TransitCalculators
 * @author Clifton Griffin <clif@objectiv.co>
 */

class UPS extends Base {
	public function __construct() {
		parent::__construct();

		add_filter( 'mg_atp_service_code_ups', array( $this, 'maybe_convert_numeric_service_code' ), 10, 1 );
	}

	function load( $license, $user_id, $password ) {
		$this->set_api( new \Ups\TimeInTransit( $license, $user_id, $password ) );

		return $this;
	}

	/**
	 * @param \DateTime $pickup_date
	 *
	 * @param $options [ $from_state, $from_city, $from_postcode, $from_country, $to_state, $to_city, $to_postcode, $to_country, $total_weight, $package_count, $cart_subtotal ]
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_transit_times_per_service( $pickup_date, $options ) {
		$return = array();

		try {
			$request = new \Ups\Entity\TimeInTransitRequest;

			$from = new \Ups\Entity\AddressArtifactFormat;
			$from->setPoliticalDivision1( $options['from_state'] );
			$from->setPoliticalDivision2( $options['from_city'] );
			$from->setPostcodePrimaryLow( $options['from_postcode'] );
			$from->setCountryCode( $options['from_country'] );
			$request->setTransitFrom( $from );

			$to = new \Ups\Entity\AddressArtifactFormat;
			$to->setPoliticalDivision1( $options['to_state'] );
			$to->setPoliticalDivision2( $options['to_city'] );
			$to->setPostcodePrimaryLow( $options['to_postcode'] );
			$to->setCountryCode( $options['to_country'] );
			$request->setTransitTo( $to );

			// Weight
			$shipment_weight = new \Ups\Entity\ShipmentWeight;
			$shipment_weight->setWeight( $options['total_weight'] );

			$unit = new \Ups\Entity\UnitOfMeasurement;
			$unit->setCode( \Ups\Entity\UnitOfMeasurement::UOM_LBS );
			$shipment_weight->setUnitOfMeasurement( $unit );
			$request->setShipmentWeight( $shipment_weight );

			// Packages
			$request->setTotalPackagesInShipment( (int) $options['package_count'] );

			// InvoiceLines
			$invoice_line_total = new \Ups\Entity\InvoiceLineTotal;
			$invoice_line_total->setMonetaryValue( $options['cart_subtotal'] );
			$invoice_line_total->setCurrencyCode( 'USD' );
			$request->setInvoiceLineTotal( $invoice_line_total );

			// Pickup date
			$request->setPickupDate( $pickup_date );

			// Get data
			$times = $this->get_api()->getTimeInTransit( $request );

			foreach ( $times->ServiceSummary as $serviceSummary ) { // phpcs:ignore
				$return[ $serviceSummary->Service->getCode() ] = new \DateTime( $serviceSummary->EstimatedArrival->getDate() ); // phpcs:ignore
				// We aren't setting the time zone here because the date is local to the delivery
			}
		} catch ( \Exception $e ) {
			error_log( 'MG Available to Promise UPS Error: ' . $e->getMessage() ); // phpcs:ignore
		}

		return $return;
	}

	/**
	 * @param $service_code
	 *
	 * @return bool|mixed
	 */
	public function maybe_convert_numeric_service_code( $service_code ) {
		$map = array(
			'01'     => '1DA',
			'01_sat' => '1DAS',
			'02'     => '2DA',
			'02_sat' => '2DAS',
			'03'     => 'GND',
			'07'     => 'ES',
			'08'     => 'EX',
			'11'     => 'ST',
			'12'     => '3DS',
			'13'     => '1DP',
			'14'     => '1DM',
			'14_sat' => '1DMS',
			'54'     => 'EP',
			'59'     => '2DM',
		);

		return isset( $map[ $service_code ] ) ? $map[ $service_code ] : $service_code;
	}

	/**
	 * @param Admin $admin
	 */
	function settings( $admin ) {
		?>
		<div style="<?php if ( $admin->plugin_instance->get_settings_manager()->get_setting( 'carrier' ) !== 'ups' ) { echo 'display:none;'; } // phpcs:ignore ?>">
			<h3>UPS Settings</h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row" valign="top">License</th>
						<td>
							<label>
								<input type="text" name="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_field_name( 'ups_license' ) ); ?>" value="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_setting( 'ups_license' ) ); ?>" /><br />
								UPS License
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">User ID</th>
						<td>
							<label>
								<input type="text" name="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_field_name( 'ups_user_id' ) ); ?>" value="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_setting( 'ups_user_id' ) ); ?>" /><br />
								User ID
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Password</th>
						<td>
							<label>
								<input type="text" name="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_field_name( 'ups_password' ) ); ?>" value="<?php echo esc_attr( $admin->plugin_instance->get_settings_manager()->get_setting( 'ups_password' ) ); ?>" /><br />
								Password
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
}
