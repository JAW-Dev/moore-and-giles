<?php
/**
 *
 * Plugin Name: MG Available to Promise
 * Description: Calculate estimated ship and delivery dates.
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPL-2.0
 * Text Domain: mg_atp
 * Domain Path: /languages
 *
 * @package    MG_ATP
 * @author     Objectiv
 * @copyright  Copyright (c) 2019, Objectiv
 * @license    GPL-2.0
 * @version    1.0.0
 */

use MG_ATP\Core\Admin;
use MG_ATP\Main;

/**
 * Autoloader
 */
include dirname( __FILE__ ) . '/vendor/autoload.php';
include dirname( __FILE__ ) . '/functions.php';

define( 'MG_ATP_PLUGIN_VERSION', '1.0.0.' );
define( 'MG_ATP_PLUGIN_DIR_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'MG_ATP_PLUGIN_DIR_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'MG_ATP_PLUGIN_PREFIX', 'mg_atp' );
define( 'MG_ATP_TESTING', false );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function mg_atp_plugin_init() {
	global $mg_atp;

	$mg_atp = Main::instance();
	$mg_atp->run();
}

mg_atp_plugin_init();

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() && ! wp_doing_ajax() ) {
	global $mg_atp_admin, $mg_atp;

	$mg_atp_admin = new Admin( $mg_atp );
}
