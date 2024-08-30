<?php
/**
 * Comments Load More
 *
 * The markup for the Single Product New tag.
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * CommentsLoadMore
 *
 * @author Jason Witt
 */
class CommentsLoadMore {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * New Tag.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public static function comments_load_more_response() {
		global $post;

		$post_id    = isset( $_POST['post_id'] ) ? wp_unslash( sanitize_text_field( $_POST['post_id'] ) ) : '';
		$setup_post = get_post( $post_id );
		$cpage      = isset( $_POST['cpage'] ) ? wp_unslash( sanitize_text_field( $_POST['cpage'] ) ) : $_POST['total_pages'];
		$per_page   = 5;
		$offset     = $cpage * $per_page;

		setup_postdata( $setup_post );

		$comments = get_comments(
			array(
				'post_id'  => $post_id,
				'orderby'  => 'meta_value_num',
				'meta_key' => 'rating',
				'offset'   => $offset,
				'number'   => 5,
			)
		);

		wp_list_comments( array( 'callback' => 'woocommerce_comments' ), $comments );
		wp_die();
	}
}
