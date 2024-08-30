<?php
/*
Plugin Name: MG Return Label
Plugin URI: http://cgd.io
Description:  Generate a shipping label on Gravity Form submit.
Version: 2.0.0
Author: Objectiv.
Author URI: https://objectiv.co

------------------------------------------------------------------------
Copyright 2009-2017 Clif Griffin Development Inc.

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

class MG_ReturnLabel {
	var $debug = false;

	public function __construct() {
		// Fetch label
		add_action( 'gform_entry_created', array( $this, 'handle_return_submit' ), 10, 2 );

		// Custom merge tag
		add_filter( 'gform_replace_merge_tags', array( $this, 'filter_merge_tags' ), 10, 7 );

		// Download link
		add_shortcode( 'return-label-link', array( $this, 'return_label_link' ) );
		add_shortcode( 'return-label-image', array( $this, 'return_label_image' ) );

		// Download Endpoint
		add_action( 'wp_ajax_nopriv_download_return_label', array( $this, 'download_return_label' ) );
		add_action( 'wp_ajax_download_return_label', array( $this, 'download_return_label' ) );

		// Add Form Settings
		add_filter( 'gform_form_settings', array( $this, 'custom_gf_form_settings' ), 10, 2 );
		add_filter( 'gform_pre_form_settings_save', array( $this, 'save_custom_gf_form_settings' ) );
	}

	function form_has_return_label( $form ) {
		return rgar( $form, 'enable_ups_return_label' ) == 'yes' ? true : false;
	}

	function handle_return_submit( $entry, $form ) {
		if ( ! $this->form_has_return_label( $form ) ) {
			return;
		}

		require_once( 'inc/class-lookup.php' );
		require_once( 'lib/RocketShipIt/autoload.php' );

		$options           = array();
		$options['config'] = new \RocketShipIt\Config();
		$options['config']->loadConfigFile( dirname( __FILE__ ) . '/lib/RocketShipIt/config.php' );

		$account_number = rgar( $form, 'ups_account' );

		if ( ! empty( $account_number ) ) {
			$options['config']->setDefault( 'ups', 'accountNumber', $account_number );
		}

		$shipment = new \RocketShipIt\Shipment( 'ups', $options );

		$countries     = MG_ReturnLabel_Lookup::countries();
		$country_zones = MG_ReturnLabel_Lookup::country_zones();
		$ship_state    = '';

		// Get State Abbreviation
		foreach ( $country_zones as $ctry => $state_info ) {
			foreach ( $state_info as $state_abbrev => $state ) {
				if ( strtolower( rgar( $entry, rgar( $form, 'from_state' ) ) ) == strtolower( $state ) ) {
					$ship_state = $state_abbrev;
					break;
				}
			}
		}

		$shipment->setParameter( 'fromName', rgar( $entry, rgar( $form, 'from_first_name' ) ) . ' ' . rgar( $entry, rgar( $form, 'from_last_name' ) ) );
		$shipment->setParameter( 'fromAddr1', rgar( $entry, rgar( $form, 'from_addr1' ) ) );
		$shipment->setParameter( 'fromAddr2', rgar( $entry, rgar( $form, 'from_addr2' ) ) );
		$shipment->setParameter( 'fromCity', rgar( $entry, rgar( $form, 'from_city' ) ) );
		$shipment->setParameter( 'fromCode', rgar( $entry, rgar( $form, 'from_postcode' ) ) );
		$shipment->setParameter( 'fromState', $ship_state );

		$shipment->setParameter( 'toCompany', rgar( $form, 'to_company' ) );
		$shipment->setParameter( 'toName', rgar( $form, 'to_name' ) );
		$shipment->setParameter( 'toPhone', rgar( $form, 'to_phone' ) );
		$shipment->setParameter( 'toAddr1', rgar( $form, 'to_addr1' ) );
		$shipment->setParameter( 'toAddr2', rgar( $form, 'to_addr2' ) );
		$shipment->setParameter( 'toCity', rgar( $form, 'to_city' ) );
		$shipment->setParameter( 'toState', rgar( $form, 'to_state' ) );
		$shipment->setParameter( 'toCode', rgar( $form, 'to_postcode' ) );

		$shipment->setParameter( 'returnCode', '9' );

		$package = new \RocketShipIt\Package( 'UPS' );
		$package->setParameter( 'length', '5' );
		$package->setParameter( 'width', '5' );
		$package->setParameter( 'height', '5' );
		$package->setParameter( 'weight', '5' );

		$shipment->addPackageToShipment( $package );

		$response = $shipment->submitShipment();

		if ( $this->debug ) {
			var_dump( $package );
		}
		if ( $this->debug ) {
			var_dump( $response );
		}
		if ( $this->debug ) {
			var_dump( $shipment->debug() );
		}
		if ( $this->debug ) {
			die;
		}

		if ( ! empty( $response['pkgs'][0]['label_img'] ) ) {
			gform_update_meta( $entry['id'], 'return_label_img', $response['pkgs'][0]['label_img'] );
		}

		if ( ! empty( $response['trk_main'] ) ) {
			gform_update_meta( $entry['id'], 'tracking_number', $response['trk_main'] );
		}

		// Set debug info
		gform_update_meta( $entry['id'], 'return_label_debug', $shipment->debug() );
	}

	function filter_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$merge_tag = '{tracking_number}';

		if ( strpos( $text, $merge_tag ) === false ) {
			return $text;
		}

		$trk_main = gform_get_meta( $entry['id'], 'tracking_number' );

		$text = str_replace( $merge_tag, $trk_main, $text );

		return $text;
	}

	function return_label_link( $atts ) {
		$atts = shortcode_atts(
			array(
				'entry' => false,
			), $atts, 'return-label-link'
		);

		if ( empty( $atts['entry'] ) ) {
			return false;
		}

		$image_data          = gform_get_meta( $atts['entry'], 'return_label_img' );
		$image_download_link = '<p style="text-align:center;"><a class="primary-button" style="margin-top:20px;" href="' . add_query_arg(
			array(
				'action' => 'download_return_label',
				'eid'    => intval( $atts['entry'] ),
			), admin_url( 'admin-ajax.php' )
		) . '" download="download">Download Your Shipping Label</a></p>';

		$output = $image_download_link;

		if ( ! empty( $image_data ) ) {
			return $output;
		} else {
			return "Return label could not be generated. This is usually caused by an invalid mailing address. Please <a href='/contact/'>contact Custom Care</a> with questions.";
		}
	}

	function return_label_image( $atts ) {
		$atts = shortcode_atts(
			array(
				'entry' => false,
			), $atts, 'return-label-link'
		);

		if ( empty( $atts['entry'] ) ) {
			return false;
		}

		$image_data = gform_get_meta( $atts['entry'], 'return_label_img' );

		$output = "<img src='data:image/gif;base64,{$image_data}' />";

		if ( ! empty( $image_data ) ) {
			return $output;
		} else {
			return "Return label could not be generated. This is usually caused by an invalid mailing address. Please <a href='/contact/'>contact Custom Care</a> with questions.";
		}
	}

	function download_return_label() {
		if ( empty( $_GET['eid'] ) ) {
			return;
		}

		$image_data = gform_get_meta( $_GET['eid'], 'return_label_img' );
		$image_data = base64_decode( $image_data );

		if ( ! empty( $image_data ) ) {
			// We'll be outputting a GIF
			header( 'Content-Type: image/gif' );

			// Set file size
			header( 'Content-length: ' . strlen( $image_data ) );

			// It will be called return_label.pdf
			header( 'Content-Disposition: attachment; filename="return_label.gif"' );

			// Output file
			echo $image_data;

			// We're done
			exit();
		}
	}

	function custom_gf_form_settings( $settings, $form ) {
		$checked = $this->form_has_return_label( $form ) === true ? 'checked' : '';

		$settings['UPS Return Label']['enable_ups_return_label'] = '
        <tr>
            <th><label for="enable_ups_return_label">Enable UPS Return Label</label></th>
            <td>
            	<input type="hidden" value="no" name="enable_ups_return_label" />
            	<input type="checkbox" value="yes" name="enable_ups_return_label" ' . $checked . '>
            </td>
        </tr>';

		$settings['UPS Return Label']['to_company'] = '
		<tr>
			<th><label for="to_company">To Company</label></th>
			<td><input value="' . rgar( $form, 'to_company' ) . '" name="to_company"></td>
		</tr>';

		$settings['UPS Return Label']['to_name'] = '
		<tr>
			<th><label for="to_name">To Name</label></th>
			<td><input value="' . rgar( $form, 'to_name' ) . '" name="to_name"></td>
		</tr>';

		$settings['UPS Return Label']['to_phone'] = '
		<tr>
			<th><label for="to_phone">To Phone</label></th>
			<td><input value="' . rgar( $form, 'to_phone' ) . '" name="to_phone"></td>
		</tr>';

		$settings['UPS Return Label']['to_addr1'] = '
		<tr>
			<th><label for="to_addr1">To Address Line 1</label></th>
			<td><input value="' . rgar( $form, 'to_addr1' ) . '" name="to_addr1"></td>
		</tr>';

		$settings['UPS Return Label']['to_addr2'] = '
		<tr>
			<th><label for="to_addr2">To Adress Line 2</label></th>
			<td><input value="' . rgar( $form, 'to_addr2' ) . '" name="to_addr2"></td>
		</tr>';

		$settings['UPS Return Label']['to_city'] = '
		<tr>
			<th><label for="to_city">To City</label></th>
			<td><input value="' . rgar( $form, 'to_city' ) . '" name="to_city"></td>
		</tr>';

		$settings['UPS Return Label']['to_postcode'] = '
		<tr>
			<th><label for="to_postcode">To Postal Code / Zip</label></th>
			<td><input value="' . rgar( $form, 'to_postcode' ) . '" name="to_postcode"></td>
		</tr>';

		$settings['UPS Return Label']['to_state'] = '
		<tr>
			<th><label for="to_state">To State</label></th>
			<td><input value="' . rgar( $form, 'to_state' ) . '" name="to_state"></td>
		</tr>';

		$settings['UPS Return Label']['ups_account'] = '
		<tr>
			<th><label for="ups_account">UPS Account (leave blank for default)</label></th>
			<td><input value="' . rgar( $form, 'ups_account' ) . '" name="ups_account"></td>
		</tr>';

		$settings['UPS Return Label']['from_first_name'] = '
		<tr>
			<th><label for="from_first_name">From First Name</label></th>
			<td>' . $this->generate_fields_select( 'from_first_name', $form ) . '</td>
		</tr>';

		$settings['UPS Return Label']['from_last_name'] = '
		<tr>
			<th><label for="from_last_name">From Last Name</label></th>
			<td>' . $this->generate_fields_select( 'from_last_name', $form ) . '</td>
		</tr>';

		$settings['UPS Return Label']['from_addr1'] = '
		<tr>
			<th><label for="from_addr1">From Address Line 1</label></th>
			<td>' . $this->generate_fields_select( 'from_addr1', $form ) . '</td>
		</tr>';

		$settings['UPS Return Label']['from_addr2'] = '
		<tr>
			<th><label for="from_addr2">From Address Line 2</label></th>
			<td>' . $this->generate_fields_select( 'from_addr2', $form ) . '</td>
		</tr>';

		$settings['UPS Return Label']['from_city'] = '
		<tr>
			<th><label for="from_city">From City</label></th>
			<td>' . $this->generate_fields_select( 'from_city', $form ) . '</td>
		</tr>';

		$settings['UPS Return Label']['from_postcode'] = '
		<tr>
			<th><label for="from_postcode">From Zip / Postal Code</label></th>
			<td>' . $this->generate_fields_select( 'from_postcode', $form ) . '</td>
		</tr>';

		$settings['UPS Return Label']['from_state'] = '
		<tr>
			<th><label for="from_state">From State</label></th>
			<td>' . $this->generate_fields_select( 'from_state', $form ) . '</td>
		</tr>';

		return $settings;
	}

	function save_custom_gf_form_settings( $form ) {
		$form['enable_ups_return_label'] = rgpost( 'enable_ups_return_label' );
		$form['to_company']              = rgpost( 'to_company' );
		$form['to_name']                 = rgpost( 'to_name' );
		$form['to_phone']                = rgpost( 'to_phone' );
		$form['to_addr1']                = rgpost( 'to_addr1' );
		$form['to_addr2']                = rgpost( 'to_addr2' );
		$form['to_city']                 = rgpost( 'to_city' );
		$form['to_postcode']             = rgpost( 'to_postcode' );
		$form['to_state']                = rgpost( 'to_state' );
		$form['from_first_name']         = rgpost( 'from_first_name' );
		$form['from_last_name']          = rgpost( 'from_last_name' );
		$form['from_addr1']              = rgpost( 'from_addr1' );
		$form['from_addr2']              = rgpost( 'from_addr2' );
		$form['from_city']               = rgpost( 'from_city' );
		$form['from_postcode']           = rgpost( 'from_postcode' );
		$form['from_state']              = rgpost( 'from_state' );
		$form['ups_account']             = rgpost( 'ups_account' );

		return $form;
	}

	function generate_fields_select( $name, $form ) {
		$result = "<select name='$name'>";

		$result .= '<option>Select Field</option>';

		foreach ( $form['fields'] as $field ) {
			if ( isset( $field['inputs'] ) ) {
				foreach ( $field['inputs'] as $f ) {
					$field_id    = $f['id'];
					$field_label = $f['label'];

					$selected = rgar( $form, $name ) == $field_id ? "selected='selected'" : '';
					$result  .= "<option value='$field_id' $selected>$field_label</option>";
				}
			} else {
				$field_id    = $field->id;
				$field_label = $field->label;

				$selected = rgar( $form, $name ) == $field_id ? "selected='selected'" : '';
				$result  .= "<option value='$field_id' $selected>$field_label</option>";
			}
		}

		$result .= '</select>';

		return $result;
	}
}

$MG_ReturnLabel = new MG_ReturnLabel();
