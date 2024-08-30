<?php
/**
 * Product Category Links Widget
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Widgets;

/**
 * Adds Product Category Links widget
 */
class ProductCategoryLinks extends \WP_Widget {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct(
			'product_category_links_widget',
			esc_html__( 'Product Category Links', 'moore-and-giles' ), // Name
			array( 'description' => esc_html__( 'A list of product categories', 'moore-and-giles' ) )
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
		global $wp_query;
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		if ( function_exists( 'get_field' ) ) {
			$product_categories = ( ! empty( obj_get_acf_field( 'woo_product_links_widget_categories', 'widget_' . $args['widget_id'] ) ) ) ? obj_get_acf_field( 'woo_product_links_widget_categories', 'widget_' . $args['widget_id'] ) : array();
			$product_tags       = ( ! empty( obj_get_acf_field( 'woo_product_links_widget_tags', 'widget_' . $args['widget_id'] ) ) ) ? obj_get_acf_field( 'woo_product_links_widget_tags', 'widget_' . $args['widget_id'] ) : array();
			$classes_field      = obj_get_acf_field( 'woo_product_links_widget_classes', 'widget_' . $args['widget_id'] );
			$classes            = ( $classes_field ) ? ' ' . $classes_field : '';

			foreach ( $product_categories as $key => $value ) {
				$query_slug = $wp_query->get_queried_object()->slug ?? '';

				if ( $query_slug === $value->slug ) {
					unset( $product_categories[ $key ] );
				}
			}

			$current_term = property_exists( $wp_query->get_queried_object(), 'term_id' ) ? $wp_query->get_queried_object()->term_id : '';

			$html = '<ul class="product_links_widget' . $classes . '">';

			if ( is_product_category() ) {
				$queried_object = get_queried_object();
				$taxonomy       = $queried_object->taxonomy;
				$term_id        = $queried_object->term_id;
				$child_terms    = get_terms( array( 'taxonomy' => $taxonomy, 'child_of' => $term_id ) );

				if ( empty( $child_terms ) && $queried_object->parent !== 0 ) {
					$child_terms    = get_terms( array( 'taxonomy' => $taxonomy, 'child_of' => $queried_object->parent ) );
				}

				if ( empty( $child_terms ) && $queried_object->parent == 0 ) {
					$child_terms = get_terms( 'product_cat', array(
						'hide_empty' => true,
						'parent'     => 0,
					) );
				}

				foreach ( $product_categories as $key => $value ) {
					foreach ( $child_terms as $child_key => $child_value ) {
						if ( $child_value->slug === $value->slug ) {
							unset( $product_categories[ $key ] );
						}
					}
				}

				if ( ! empty( $child_terms ) ) {
					foreach ( $child_terms as $child_term ) {
						$term  = get_term_by( 'id', $child_term->term_id, $taxonomy );
						$id    = $term->term_id;
						$name  = $term->name;
						$link  = get_term_link( $id );
						$html .= $this->list_items( $current_term, $id, $name, $link );
					}
				}
			} else {
				$categories = get_terms( 'product_cat', array(
					'hide_empty' => true,
					'parent'     => 0,
				) );

				usort( $categories, function($a, $b) {
					return strcmp($a->name, $b->name);
				} );

				foreach ( $product_categories as $key => $value ) {
					foreach ( $categories as $category_key => $category_value ) {
						if ( $category_value->slug === $value->slug ) {
							unset( $product_categories[ $key ] );
						}
					}
				}

				foreach ( $categories as $category ) {
					$id           = $category->term_id;
					$name         = $category->name;
					$link         = get_term_link( $id );
					$current_term = '';
					$html        .= $this->list_items( $current_term, $id, $name, $link );
				}
			}

			if ( $product_categories ) {
				foreach ( $product_categories as $category ) {
					$term_link = get_term_link( $category->term_id );
					$html     .= $this->list_items( $current_term, $category->term_id, $category->name, $term_link );
				}
			}

			if ( $product_tags ) {
				foreach ( $product_tags as $tag ) {
					$term_link = get_term_link( $tag->term_id );
					$html     .= $this->list_items( $current_term, $tag->term_id, $tag->name, $term_link );
				}
			}
			$html .= '</ul>';
		}

		echo wp_kses_post( $html );
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * List Items
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function list_items( $current_term, $id, $name, $link ) {
		if ( $current_term === $id ) {
			$html = '<li><strong><a href="' . $link . '">' . $name . '</a></strong></li>';
		} else {
			$html = '<li><a href="' . $link . '">' . $name . '</a></li>';
		}

		return $html;
	}

	/**
	 * form
	 *
	 * @author Jason Witt
	 *
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance ) {
		$title = '';
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * update
	 *
	 * @author Jason Witt
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return void
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}
