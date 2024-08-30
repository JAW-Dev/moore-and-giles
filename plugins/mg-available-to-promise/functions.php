<?php
/**
 * @param $service_code
 * @param $carrier
 *
 * @return bool|\DateTime
 */
function mg_get_atp( $service_code, $carrier ) {
	$return = false;

	try {
		$dm               = new \MG_ATP\Core\DateManipulator( $service_code, $carrier );
		$transit_times    = $dm->get_delivery_dates();
		$new_service_code = apply_filters( 'mg_atp_service_code_' . strtolower( $carrier ), $service_code );

		// If Saturday delivery isn't an option, try falling back to the normal service level
		if ( ! isset( $transit_times[ $new_service_code ] ) ) {
			$service_code     = str_replace( '_sat', '', $service_code );
			$new_service_code = apply_filters( 'mg_atp_service_code_' . strtolower( $carrier ), $service_code );
		}

		$return = isset( $transit_times[ $new_service_code ] ) ? $transit_times[ $new_service_code ] : false;
	} catch ( \Exception $e ) {
		error_log( 'mg_get_atp error: ' . $e->getMessage() );
	}

	return apply_filters( 'mg_get_atp', $return );
}
