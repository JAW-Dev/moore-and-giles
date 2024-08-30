<?php
/**
 * Plugin Name: Blocked Plugins
 * Description: Prevent certain plugins from loading using WP_BLOCKED_PLUGINS constant.
 * Author:      Clifton Griffin
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( defined( 'WP_BLOCKED_PLUGINS' ) && is_array( WP_BLOCKED_PLUGINS ) ) {
	$WP_BLOCKED_PLUGINS = WP_BLOCKED_PLUGINS;

	add_filter('option_active_plugins', function( $active_plugins ) use( $WP_BLOCKED_PLUGINS ) {
		$active_plugins = array_diff( $active_plugins, $WP_BLOCKED_PLUGINS );

		return $active_plugins;
	} );
}