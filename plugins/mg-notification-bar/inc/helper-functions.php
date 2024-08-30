<?php

add_filter( 'body_class', 'mg_notification_bar_body_class' );
function mg_notification_bar_body_class( $classes ) {
	$enable = mg_notification_bar_get_option('enable_notification_bar');

	if ( $enable == 'on' ) {
		$classes[] = 'notification-bar-is-present';
	} else {
		$classes[] = '';
	}
	return $classes;
}

function mg_display_notification_bar() {
	$enable = mg_notification_bar_get_option('enable_notification_bar');
	$text = mg_notification_bar_get_option('notification_bar_text');
	$link = mg_notification_bar_get_option('notification_bar_link');
	$link_text = mg_notification_bar_get_option('notification_bar_link_text');
	$link_color = mg_notification_bar_get_option('notification_bar_link_color');
	$color = mg_notification_bar_get_option('notification_bar_color');

	if ( $enable == 'on' ) {
		echo '<div class="notification-bar" style="background-color:'.$color.'">';
			echo '<p class="notification-bar-text">' . $text;
				if ( ! empty( $link ) && ! empty( $link_text ) ) {
					echo '<a href="'. $link .'" style="color: '.$link_color.';">' . $link_text . '</a>';
				}
			echo '</p>';
		echo '</div>';
	}

}

add_action( 'genesis_header', 'mg_display_notification_bar' );
