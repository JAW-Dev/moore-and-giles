<?php
/**
 * Custom Title
 *
 * @author     Jason Witt
 */
namespace Objectiv\Site\Widgets;

/**
 * CustomTitle
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class CustomTitle extends \WP_Widget {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			'custom_title',
			esc_html__( 'Title', 'moore-and-giles' ), // Name
			array( 'description' => esc_html__( 'Add a custom title to the sidebar' ), )
		);
	}

	/**
	 * Register.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public static function register() {
		register_widget( __CLASS__ );
	}

	/**
	 * widget
	 * 
	 * @author Jason Witt
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( function_exists( 'get_field' ) ) {
			$title              = obj_get_acf_field('woo_custom_title_widget_title', 'widget_' . $args['widget_id']);
			$classes_field      = obj_get_acf_field('woo_custom_title_widget_classes', 'widget_' . $args['widget_id']);
			$classes            = ( $classes_field ) ? ' ' . $classes_field : '';
			$html = '<h2 class="custom-title' . $classes . '">' . $title . '</h2>';
		}
		
		echo wp_kses_post($html);
		echo $args['after_widget'];
	}

	/**
	 * form
	 * 
	 * @author Jason Witt
	 *
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance ) {}

	/**
	 * update
	 * 
	 * @author Jason Witt
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return void
	 */
	public function update( $new_instance, $old_instance ) {}
}
