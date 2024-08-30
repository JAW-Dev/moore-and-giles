<?php
/**
 * Get Post.
 *
 * Load: true
 *
 * @package    MG_WooCommerce_Custom_Fields
 * @subpackage MG_WooCommerce_Custom_Fields/Inlcudes/Functions/Example
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! function_exists( 'mg_wcf_get_post' ) ) {
	/**
	 * Get Post
	 *
	 * Get the $_POST.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array
	 */
	function mg_wcf_get_post() {
		$post = new MG_WooCommerce_Custom_Fields\Includes\Classes\Post();
		return $post->get();
	}
}
