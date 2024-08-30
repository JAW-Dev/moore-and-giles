<?php

class MG_Admin_Customer_Merge {
	public function __construct() {
		add_action('admin_menu', array($this, 'setup_menu_page'), 100 );
		add_action('admin_init', array($this, 'process_merge') );
	}

	function setup_menu_page() {
		add_submenu_page( "shopp-orders", "Customer Merge", "Customer Merge", "manage_options", "mg-customer-merge", array($this, "admin_page") );
	}

	function admin_page() {
		?>
		<div class="wrap">
			<h2>Customer Merge</h2>

			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <?php wp_nonce_field( 'merge_customers', '_mg_merge_nonce' ); ?>
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row" valign="top">
							<label for="mg_merge_customer_source"><?php _e('Customer Source', 'mg_core_functionality'); ?></label>
						</th>
						<td>
							<label><input type="text" name="mg_merge_customer_source" id="mg_merge_customer_source" /> <br/>
								We will merge his customer into the destination customer. This customer will be deleted.
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="mg_merge_customer_destination"><?php _e('Customer Destination', 'mg_core_functionality'); ?></label>
						</th>
						<td>
							<label><input type="text" name="mg_merge_customer_destination" id="mg_merge_customer_destination" /> <br/>
								The destination customer we are merging into. This is the customer that we will keep.
							</label>
						</td>
					</tr>
                    <tr>
                        <th scope="row" valign="top">
                            <label for="mg_merge_use_newer_login"><?php _e('Login Option', 'mg_core_functionality'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="mg_merge_use_source_login" value="no" />
                            <label><input type="checkbox" name="mg_merge_use_source_login" id="mg_merge_use_source_login" value="yes" /> <?php _e('Use login from source record?', 'mg_core_functionality'); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top">
                            <label for="mg_merge_replace_destination_addresses"><?php _e('Addresses', 'wp_sent_mail'); ?></label>
                        </th>
                        <td>
                            <input type="hidden" name="mg_merge_replace_destination_addresses" value="no" />
                            <label><input type="checkbox" name="mg_merge_replace_destination_addresses" id="mg_merge_replace_destination_addresses" value="yes" /> <?php _e('Replace destination addresses?', 'mg_core_functionality'); ?></label>
                            <p>By default, all address records from source will be reassigned to destination. It will usually be preferable to replace the address records on the destination record.</p>
                        </td>
                    </tr>
					</tbody>
				</table>

				<?php submit_button('Merge Customers'); ?>
			</form>
		</div>
		<?php
	}

	function process_merge() {
	    global $wpdb;
		$prefix = $wpdb->get_blog_prefix();

        if ( isset($_REQUEST['_mg_merge_nonce']) && wp_verify_nonce( $_REQUEST['_mg_merge_nonce'], 'merge_customers' ) ) {
            $source_id = $_REQUEST['mg_merge_customer_source'];
	        $destination_id = $_REQUEST['mg_merge_customer_destination'];
	        $login_option = $_REQUEST['mg_merge_use_source_login'];
	        $replace_addresses = $_REQUEST['mg_merge_replace_destination_addresses'];

	        if ( empty($source_id) || empty($destination_id) ) {
	            wp_die('A source and destination are required for merging');
            }

            if ( $source_id === $destination_id ) {
	            wp_die('Source and destination must be different.');
            }

            if ( false === shopp_customer($source_id) ) {
	            wp_die('Source customer ID is not a valid customer ID.');
            }

	        if ( false === shopp_customer($destination_id) ) {
		        wp_die('Destination customer ID is not a valid customer ID.');
	        }

	        // Does customer have addresses?
	        $addresses = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$prefix}shopp_address WHERE customer = %d", $source_id) );

	        if ( count($addresses) > 0 ) {
	            if ( $replace_addresses == "yes" ) {
		            // Delete existing addresses for destination customer
		            $wpdb->query("DELETE FROM {$prefix}shopp_address WHERE customer = $destination_id");
                }

                // Switch source addresses to use destination customer
                $wpdb->query("UPDATE {$prefix}shopp_address SET customer = {$destination_id} WHERE customer = {$source_id}");
	        }

	        // Update bag orders
	        $wpdb->query("UPDATE {$wpdb->prefix}shopp_purchase SET customer = {$destination_id} WHERE customer = {$source_id}");

	        // Update leather orders
	        $wpdb->query("UPDATE wp_3_shopp_purchase SET customer = {$destination_id} WHERE customer = {$source_id}");

	        // Clear bag meta
	        $wpdb->query("DELETE FROM {$wpdb->prefix}shopp_meta WHERE context = 'customer' AND parent = {$source_id}");
	        $wpdb->query("DELETE FROM wp_3_shopp_meta WHERE context = 'customer' AND parent = {$source_id}");

	        if ( "yes" == $login_option ) {
                $source_customer = shopp_customer( $source_id );
                $destination_customer = shopp_customer( $destination_id );

                $destination_customer->wpuser = $source_customer->wpuser;
                $destination_customer->save();
	        }

	        // Delete duplicate
	        shopp_rmv_customer($source_id);

	        add_action( 'admin_notices', function () {
		        ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( "Customer {$source_id} merged into customer {$destination_id}", 'mg_core_functionality' ); ?></p>
                </div>
		        <?php
	        } );
        }
    }
}