<?php
/*
Plugin Name: MG Sample Requests
Description:  An internal tool for quickly building and submitting sample requests.
Version: 1.0
Author: CGD Inc.
Author URI: http://cgd.io
*/

define( 'MG_SR_FILE', __FILE__ );
define( 'MG_SR_PATH', dirname( __FILE__ ) );

class MG_SampleRequests {

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		require_once( 'inc/class-process-sample-requests.php' );
		new MG_SampleRequests_Process();

		require_once( 'inc/class-frontend.php' );
		new MG_SampleRequests_Frontend();

		require_once( 'inc/class-admin.php' );
		new MG_SampleRequests_Admin();
	}

	function activation() {
		wp_clear_scheduled_hook( 'do_unfulfilled_reminders' );
		wp_schedule_event( time(), 'daily', 'do_unfulfilled_reminders' );
	}

	function deactivation() {
		wp_clear_scheduled_hook( 'do_unfulfilled_reminders' );
	}
}

$MG_SampleRequests = new MG_SampleRequests();
