<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.mooreandgiles.com
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       MG Core
 * Plugin URI:        http://www.mooreandgiles.com
 * Description:       Modular extensions for MG site.
 * Version:           1.0.0
 * Author:            Objectiv
 * Author URI:        https://objectiv.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mg-core
 * Domain Path:       /languages
 */

namespace MG_Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// We load Composer's autoload file
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
	utils\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name() {
	utils\Deactivator::deactivate();
}

register_activation_hook( __FILE__, '\MG_Core\activate_plugin_name' );
register_deactivation_hook( __FILE__, '\MG_Core\deactivate_plugin_name' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_plugin_name() {
	$plugin = new Main();
	$plugin->run();
}
run_plugin_name();
