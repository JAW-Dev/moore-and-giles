<?php
/**
 * Plugin Name: MG Shopp REST
 * Plugin URI: https://objectiv.co
 * Description: Add REST API endpoint to WordPress for some Shopp functions.
 * Version: 1.0.0
 * Author: Objectiv
 * Author URI: https://objectiv.co
 */

require plugin_dir_path( __FILE__ ) . 'inc/class-mg-shopp-rest.php';

$mg_shopp_rest = new MG_Shopp_Rest();

