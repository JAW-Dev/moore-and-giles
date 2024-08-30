<?php

/**
 * Created by PhpStorm.
 * User: clifgriffin
 * Date: 6/6/17
 * Time: 12:43 PM
 */
class MG_ShoppCSVSpecImport extends WordPress_SimpleSettings {
	var $prefix = "_mg_scsi_";

	public function __construct() {
		parent::__construct();

		// Silence is golden
	}

	public function start() {
		add_action('admin_menu', array($this, 'admin_menu'), 100 );
		add_action("{$this->prefix}_settings_saved", array($this, 'run_import') );
	}

	function admin_menu() {
		add_submenu_page( "shopp-products", "Spec CSV Import", "Spec CSV Import", "manage_options", "mg-shopp-scv-spec-import", array($this, "admin_page") );
	}

	function admin_page() {
		?>
		<div class="wrap">
			<h2>Shopp CSV Import</h2>

			<form name="settings" id="mg_gwp" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                <?php $this->the_nonce(); ?>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row" valign="top">CSV URL</th>
                            <td>
                                <label>
                                    <input size="55" type="text" name="<?php echo $this->get_field_name('csv_url'); ?>" value="<?php echo $this->get_setting('csv_url'); ?>" /><br />
                                    URL of CSV to import.
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" valign="top">Replace</th>
                            <td>
                                <label>
                                    <input type="hidden" name="<?php echo $this->get_field_name('replace'); ?>" value="no" />
                                    <input type="checkbox" name="<?php echo $this->get_field_name('replace'); ?>" value="yes" <?php if ( $this->get_setting('replace') == "yes") echo 'checked="checked"'; ?> />	Remove and replace specs on each affected product.
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button('Save and Import'); ?>
            </form>
		</div>
		<?php
	}

	function run_import() {
	    // Download CSV
        $url = $this->get_setting('csv_url');
        if ( empty($url) || ! filter_var($url, FILTER_VALIDATE_URL) ) {
	        die('URL is not valid.');
        }

		$response = wp_remote_get( $url );
        if ( ! is_array($response) ) {
            die('Failed to download URL.');
        }

        // Convert CSV string into an associated array
        // Use fgetscsv since it can handle new lines in columns
		$spec_data = [];
		$stream = fopen( 'php://memory', 'r+' );
		fwrite( $stream, $response['body'] );
		rewind( $stream );

		while ($row = fgetcsv($stream)) {
			$spec_data[] = $row;
		}

		$header = array_shift($spec_data);
		array_walk($spec_data, array($this, '_combine_array'), $header);

        // Clear existing specs, if enabled
        if ( $this->get_setting('replace') == "yes" ) {
	        foreach( $spec_data as $row ) {
		        $product        = shopp_product( $row['Leather Name'], 'name' );
		        $existing_specs = shopp_product_specs( $product->id );

		        foreach ( $existing_specs as $es_name => $es_value ) {
			        shopp_product_rmv_spec( $product->id, $es_name );
		        }
	        }
        }

        // Import specs
		foreach( $spec_data as $row ) {
			$Product = shopp_product( $row['Leather Name'], 'name' );

			if ( ! is_a( $Product, 'ShoppProduct' ) ) {
				error_log('FAILED SPEC IMPORT: ' . $row['Leather Name'] );
				continue;
			}

			// Combine features
            $row = $this->combine_features($row);

			// Skip empty specs
			$row = array_filter( $row, 'strlen' );

			// Remove name
			unset($row['Leather Name']);

            if ( ! empty($row['Summary']) ) {
	            $Product->summary = $row['Summary'];

	            // Save the product
	            $Product->save();
            }

			// Remove summary
			unset($row['Summary']);

            reset($row);

            foreach( $row as $key => $value ) {
	            shopp_product_set_spec( $Product->id, $key, $value );
            }

			// Add the specs
			shopp_product_set_specs($Product->id, $row);
        }
    }

	function _combine_array(&$row, $key, $header) {
		$row = array_combine($header, $row);
	}

	function combine_features( $row ) {
	    $features = [];

	    foreach( $row as $key => $value ) {
            if ( stripos($key, 'Feature') !== false ) {
                $features[] = $value;

                // Remove from row
                unset( $row[ $key ] );
            }
        }

        if ( ! empty($features) ) {
	        $row['Features'] = implode(PHP_EOL, $features);
        }

        return $row;
    }
}