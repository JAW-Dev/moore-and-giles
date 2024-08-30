<?php
/*
Plugin Name: MG Nextwoogen
Description:  Automagically link associated next gen gallery with woo slider.
Version: 1.0
Author: Clif Griffin Development Inc.
Author URI: http://cgd.io
*/

class MG_Nextwoogen {
	function __construct () {
		add_filter('wooslider_get_slides', array($this, 'nextgen_slides'), 100, 4);
		add_shortcode('slick-nextgen', array($this, 'slick_nextgen') );
	}

	function nextgen_slides ($slides, $type, $args, $settings) {
		global $post;
		$gallery_id = get_post_meta($post->ID, 'gallery_id', true);
		$mg_gallery_id = get_post_meta($post->ID, '_mg_gallery_id', true);

		if ( !empty($mg_gallery_id) ) $gallery_id = $mg_gallery_id;

		if ( !empty($gallery_id) ) {
			$ngg_options = nggGallery::get_option('ngg_options');
			$picturelist = nggdb::get_gallery($gallery_id, $ngg_options['galSort'],$ngg_options['galSortDir']);

			if ( count($picturelist) == 0 ) return $slides;

			foreach($picturelist as $img) {
				$slides[] = array('content' => sprintf('<img src="%s" title="%s" alt="%s" />', $img->imageURL, $img->title, $img->alttext) );
			}
		}

		return $slides;
	}

	function slick_nextgen($atts) {
		global $post;

		$gallery_id = get_post_meta($post->ID, 'gallery_id', true);
		$mg_gallery_id = get_post_meta($post->ID, '_mg_gallery_id', true);

		if ( !empty($mg_gallery_id) ) $gallery_id = $mg_gallery_id;

		$atts = shortcode_atts( array(
			'gallery' => $gallery_id,
		), $atts, 'slick-nextgen' );

		$result = '';

		$mg_gallery_id = $atts['gallery'];

		$ngg_options = nggGallery::get_option('ngg_options');
		$picturelist = nggdb::get_gallery($mg_gallery_id, $ngg_options['galSort'],$ngg_options['galSortDir']);

		if ( count($picturelist) == 0 ) return;

		$result .= "<div class='slick-nextgen-gallery'>";
			foreach($picturelist as $img) {
				$result .= sprintf('<img src="%s" title="%s" alt="%s" />', $img->imageURL, $img->title, $img->alttext);
			}
		$result .= "</div>";

		$result .= "<script>jQuery(document).ready(function() {jQuery('.slick-nextgen-gallery').slick({arrows: true}) });</script>";

		return $result;
	}
}

$MG_Nextwoogen = new MG_Nextwoogen();
