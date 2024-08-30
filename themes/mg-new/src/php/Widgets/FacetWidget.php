<?php

namespace Objectiv\Site\Widgets;

use Objectiv\Site\Base\ObjectivWidget;

class FacetWidget extends ObjectivWidget {
	public function __construct( array $widget_options = array(), array $control_options = array() ) {
		parent::__construct( 'facet_widget', __('Facet Widget', 'moore-and-giles'), $widget_options, $control_options );
	}

	public function widget( $args, $instance ) {
		$short_code = apply_filters( 'facet_widget_shortcode', $instance['shortcode'] );

//		echo $args['before_widget'];

//		if ( ! empty( $short_code ) )
//			echo $args['before_shortcode'] . $short_code . $args['after_shortcode'];

		d($short_code);
		echo do_shortcode($short_code);


//		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'shortcode' ] ) ) {
			$short_code = $instance[ 'shortcode' ];
		}
		else {
			$short_code = __( 'Add shortcode here', 'moore-and-giles' );
		}

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'shortcode' ); ?>"><?php _e( 'Shortcode:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'shortcode' ); ?>" name="<?php echo $this->get_field_name( 'shortcode' ); ?>" type="text" value="<?php echo esc_attr( $short_code ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['shortcode'] = ( ! empty( $new_instance['shortcode'] ) ) ? $new_instance['shortcode'] : '';

		return $instance;
	}
}