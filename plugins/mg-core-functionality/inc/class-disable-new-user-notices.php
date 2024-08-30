<?php

class MG_DisableNewUserNotices {
	public function __construct() {
		remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
		remove_action( 'edit_user_created_user', 'wp_send_new_user_notifications', 10 );
		remove_action( 'network_site_new_created_user', 'wp_send_new_user_notifications' );
		remove_action( 'network_site_users_created_user', 'wp_send_new_user_notifications' );
		remove_action( 'network_user_new_created_user', 'wp_send_new_user_notifications' );

		add_action( 'register_new_user', array( $this, 'wp_send_new_user_notifications' ) );
		add_action( 'edit_user_created_user', array( $this, 'wp_send_new_user_notifications' ), 10, 2 );
		add_action( 'network_site_new_created_user', array( $this, 'wp_send_new_user_notifications' ) );
		add_action( 'network_site_users_created_user', array( $this, 'wp_send_new_user_notifications' ) );
		add_action( 'network_user_new_created_user', array( $this, 'wp_send_new_user_notifications' ) );
	}

	function wp_send_new_user_notifications( $user_id, $notify = 'user' ) {
		wp_send_new_user_notifications( $user_id, $notify );
	}
}
