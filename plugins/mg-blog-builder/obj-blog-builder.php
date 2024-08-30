<?php
/*
Plugin Name: Objectiv Blog Builder
Plugin URI: http://www.mooreandgiles.com
Description: Easily build components for your blog
Version: 1.0.0
Author: Objectiv
Author URI: http://objectiv.co

------------------------------------------------------------------------
Copyright 2009-2016 Objectiv

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OBJ_BLOG_BUILDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once 'inc/class-gamajo-template-loader.php';
require_once 'inc/class-obj-blog-builder-template-loader.php';
require_once 'inc/class-obj-blog-builder-setting-field.php';
require_once 'inc/class-obj-blog-builder-create-shortcode.php';

class OBJ_Blog_Builder {
	public function __construct() {
		$this->file    = __FILE__;
		$this->version = 1.0;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_styles' ) );
		add_action( 'media_buttons', array( $this, 'add_media_button' ) );
		add_filter( 'wp_fullscreen_buttons', array( $this, 'fs_add_media_button' ) );
		add_action( 'admin_footer', array( $this, 'popup' ) );
		add_action( 'init', array( $this, 'create_shortcodes' ) );

		add_image_size( 'obj_three_four_image', 530, 795, true );
		add_image_size( 'obj_one_one_image', 600, 600, true );

	}

	/**
	 * Check to see if page has a shortcode
	 * @param  string  $shortcode
	 * @return boolean
	 *
	 * @since 1.0
	 */
	private function has_shortcode( $shortcode = '' ) {

		if ( is_singular( 'post' ) ) {

			global $post;
			$post_obj = get_post( $post->ID );
			$found    = false;

			if ( ! $shortcode ) {
				return $found;
			}

			if ( stripos( $post_obj->post_content, '[' . $shortcode ) !== false ) {
				$found = true;
			}

			return $found;
		}

	}

	/**
	 * Enqueue admin scripts and styles
	 */
	public function admin_scripts_styles() {

		wp_enqueue_media();
		wp_enqueue_script( 'blog-admin', plugins_url( '/assets/js/admin.js', $this->file ), array( 'jquery' ), $this->version, true );
		wp_enqueue_style( 'blog-admin-css', plugins_url( '/assets/css/admin.css', $this->file ), false, $this->version );

	}

	/**
	 * Enqueue public scripts
	 */
	public function scripts_styles() {
		wp_enqueue_style( 'blog-public-css', plugins_url( '/assets/css/public.css', $this->file ), false, $this->version );
		wp_enqueue_script( 'blog-public', plugins_url( '/assets/js/public.js', $this->file ), array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'img-liquid', plugins_url( '/vendor/imgLiquid/js/imgLiquid-min.js', $this->file ), array( 'jquery' ), $this->version, true );

		if ( $this->has_shortcode( 'blog_module_gallery-1-by-2' ) || $this->has_shortcode( 'blog_module_gallery-2-by-2' ) ) {
				// TODO: Only enqueue when needed but make sure public.js doesn't need it on other pages!
		}

		if ( $this->has_shortcode( 'blog_module_tweet' ) ) {
			wp_enqueue_script( 'twitter-intent', 'https://platform.twitter.com/widgets.js', array(), $this->version, true );
		}
	}

	/**
	 * Array of shortcodes
	 * We loop through these to create the setting fields and the shortcodes
	 */
	private function shortcodes() {

		$shortcodes = array(
			'image'          => array(
				'name' => 'Image',
				'desc' => 'Create image component in the blog post',
				'atts' => array(
					'img'     => array(
						'type'    => 'media_upload',
						'default' => '',
						'desc'    => 'Image URL',
					),
					'img_id'  => array(
						'type'    => 'text',
						'default' => '',
						'desc'    => 'Image ID',
					),
					'caption' => array(
						'type'    => 'textarea',
						'default' => '',
						'desc'    => 'Caption',
					),
				),
			),
			'gallery-1-by-2' => array(
				'name' => '1x2 Grid Gallery',
				'desc' => 'Create a simple grid gallery',
				'atts' => array(
					'img_1'    => array(
						'type'    => 'media_upload',
						'default' => '',
						'desc'    => 'Image URL',
					),
					'img_1_id' => array(
						'type'    => 'text',
						'default' => '',
						'desc'    => 'Image Id',
					),
					'img_2'    => array(
						'type'    => 'media_upload',
						'default' => '',
						'desc'    => 'Image URL',
					),
					'img_2_id' => array(
						'type'    => 'text',
						'default' => '',
						'desc'    => 'Image ID',
					),
				),
			),
			'gallery-2-by-2' => array(
				'name' => '2x2 Grid Gallery',
				'desc' => 'Create a simple grid gallery',
				'atts' => array(
					'img_1'    => array(
						'type'    => 'media_upload',
						'default' => '',
						'desc'    => 'Image URL',
					),
					'img_1_id' => array(
						'type'    => 'text',
						'default' => '',
						'desc'    => 'Image Id',
					),
					'img_2'    => array(
						'type'    => 'media_upload',
						'default' => '',
						'desc'    => 'Image URL',
					),
					'img_2_id' => array(
						'type'    => 'text',
						'default' => '',
						'desc'    => 'Image ID',
					),
					'img_3'    => array(
						'type'    => 'media_upload',
						'default' => '',
						'desc'    => 'Image URL',
					),
					'img_3_id' => array(
						'type'    => 'text',
						'default' => '',
						'desc'    => 'Image Id',
					),
					'img_4'    => array(
						'type'    => 'media_upload',
						'default' => '',
						'desc'    => 'Image URL',
					),
					'img_4_id' => array(
						'type'    => 'text',
						'default' => '',
						'desc'    => 'Image ID',
					),
				),
			),
			'tweet'          => array(
				'name' => 'Click To Tweet',
				'desc' => 'Create "Click To Tweet" component in the blog post',
				'atts' => array(
					'text' => array(
						'type'    => 'textarea',
						'default' => '',
						'desc'    => 'Tweet text',
					),
				),
			),
			'inline-tweet'   => array(
				'name' => 'Inline Click To Tweet',
				'desc' => 'Create an inline "Click To Tweet" component',
				'atts' => array(
					'text' => array(
						'type'    => 'textarea',
						'default' => '',
						'desc'    => 'Tweet text',
					),
				),
			),
		);

		$shortcodes = apply_filters( 'obj_blog_builder_shortcodes', $shortcodes );

		return $shortcodes;

	}

	/**
	 * Add media button for regular edit screen
	 */
	public function add_media_button() {
		echo '<a href="#" id="insert-blog-component" class="obj-blog-builder-button button"><span class="obj-admin-button-icon dashicons dashicons-text"></span> Add Blog Component</a>';
	}

	/**
	 * Add media button for full screen mode
	 */
	public function fs_add_media_button() {
		echo '<a href="#" id="insert-blog-component" class="obj-blog-builder-button button"><span class="obj-admin-button-icon dashicons dashicons-text"></span> Add Blog Component</a>';
	}

	/**
	 * Popup
	 */
	public function popup() {

		global $pagenow;
		$shortcodes = $this->shortcodes();

		$blog_builder_load_on = apply_filters( 'blog_builder_loads_on', array( 'post.php' ) );

		if ( in_array( $pagenow, $blog_builder_load_on ) ) { ?>
			<div id="obj-blog-builder-wrap">
				<div id="obj-blog-builder">
					<a id="blog-builder-close" class="media-modal-close blog-builder-close-modal" href="#">
						<span class="media-modal-icon"><span class="screen-reader-text">Close builder popup</span></span>
					</a>
					<div id="obj-blog-builder-panel">
						<a id="blog-builder-panel-close" class="media-modal-close blog-builder-close-panel" href="#">
							<span class="media-modal-icon"><span class="screen-reader-text">Close settings panel</span></span>
						</a>
						<div class="blog-builder-pannel-inner">
							<?php foreach ( $shortcodes as $key => $shortcode ) : ?>
								<div id="settings-<?php echo $key; ?>">
									<h3><?php echo $shortcode['name']; ?> Settings</h3>
									<?php new OBJ_Blog_Builder_Setting_Field( $key, $shortcode['atts'] ); ?>
								</div>
							<?php endforeach; ?>
							<input type="hidden" id="blog-builder-shortcode-result" val="" />
						</div>
						<footer class="blog-builder-panel-footer">
							<a href="#" id="blog-builder-insert" class="button button-primary">Insert Componet</a>
						</footer>
					</div>
					<div id="obj-blog-builder-content">
						<div class="blog-builder-inner-wrap">
							<ul class="blog-builder-modules">
								<?php foreach ( $shortcodes as $key => $shortcode ) : ?>
									<li class="blog-builder-module <?php echo $key; ?>">
										<a href="#" data-module-value="<?php echo $key; ?>">
											<?php echo $shortcode['name']; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?php
		}

	}

	public function create_shortcodes() {

		$shortcodes = $this->shortcodes();
		foreach ( $shortcodes as $key => $shortcode ) {
			new OBJ_Blog_Builder_Create_Shortcode( $key, $shortcode, __FILE__ );
		}

	}

}

$obj_blog_builder = new OBJ_Blog_Builder();
