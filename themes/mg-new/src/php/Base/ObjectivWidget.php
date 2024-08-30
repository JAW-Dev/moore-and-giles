<?php

namespace Objectiv\Site\Base;

class ObjectivWidget extends \WP_Widget {
	public function __construct( $id_base, $name, array $widget_options = array(), array $control_options = array() ) {
		parent::__construct( $id_base, $name, $widget_options, $control_options );

		die;

		add_action( 'widgets_init', array($this, 'load') );
	}

	public function load() {
		register_widget(get_called_class());
	}

	public function widget( $args, $instance ) {}

	public function form( $instance ) {}
}
