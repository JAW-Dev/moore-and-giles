<?php
/**
 * Plugin Constants.
 *
 * @package    Mg_Admin
 * @subpackage Mg_Admin/Inlcudes
 * @author     Objectiv
 * @copyright  Copyright (c) 2020, Objectiv
 * @license    GNU General Public License v2 or later
 * @since      1.0.0
 */

if ( ! defined( 'MGADMIN_VERSION' ) ) {
	define( 'MGADMIN_VERSION', '1.0.0.' );
}

if ( ! defined( 'MGADMIN_DIR_URL' ) ) {
	define( 'MGADMIN_DIR_URL', trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) ) );
}

if ( ! defined( 'MGADMIN_DIR_PATH' ) ) {
	define( 'MGADMIN_DIR_PATH', trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ) );
}

if ( ! defined( 'MGADMIN_PRFIX' ) ) {
	define( 'MGADMIN_PRFIX', 'Mg_Admin' );
}
