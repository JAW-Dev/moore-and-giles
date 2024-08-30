<?php
/*
Plugin Name: MG Nearby Retailers
Plugin URI: http://cgd.io
Description:  Display nearby retailers on receipts.
Version: 1.0.0
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2009-2011 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

class MG_NearbyRetailers {

	public function __construct() {
		add_filter('shopp_themeapi_purchase_nearbyretailersjson', array($this, 'nearby_retailers'), 10, 3);
	}

	function nearby_retailers($result, $options, $Purchase) {

		// Get Geocode
		$args = array(
			'address' => $Purchase->address . ' ' .$Purchase->city . ', ' . $Purchase->state . ' ' . $Purchase->zip,
			//'key'  => $this->api_key,
		);

		$response = wp_remote_get('https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($args) );

		if ( is_a($response, 'WP_Error') ) return;
		
		$location = json_decode($response['body']);
		$geometry = $location->results[0]->geometry->location;

		// Get Simplemap Results
		$s_args = array(
			'sm-xml-search' => 1,
			'lat'           => $geometry->lat,
			'lng'           => $geometry->lng,
			'radius'        => 60,
			'namequery'     => $Purchase->city . ', ' . $Purchase->state,
			'query_type'    => 'all',
			'limit'         => 3,
			'sm_category'   => '',
			'sm_tag'        => '',
			'address'       => '',
			'city'          => $Purchase->city,
			'state'         => $Purchase->state,
		);

		$s_response = wp_remote_get(get_site_url() . '?' . http_build_query($s_args), array('sslverify' => false) );

		if ( ! is_a($s_response, 'WP_Error') ) {
			$s_response = json_decode($s_response['body']);

			return $s_response;
		}

		return $result;
	}
}

$MG_NearbyRetailers = new MG_NearbyRetailers();
