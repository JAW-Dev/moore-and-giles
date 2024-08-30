<?php
/*
Plugin Name: MG Shopp Gallery
Description:  Modifications to default Shopp gallery.
Version: 1.0
Author: Clif Griffin Development Inc.
Author URI: http://cgd.io
*/

class MG_ShoppGallery {
	function __construct() {
		add_action('wp', array($this, 'init') );
		add_filter('wooslider_get_slides', array($this, 'shopp_wooslider'), 10, 4);
	}

	function init() {
		remove_all_filters('shopp_themeapi_product_gallery');
		add_filter('shopp_themeapi_product_gallery', array($this, 'gallery'), 10, 3 );

		add_filter('shopp_themeapi_product_zoomgallery', array($this, 'zoomgallery_mobile'), 10, 3 );
	}

	public function shopp_wooslider($slides, $type, $args, $settings) {
		global $blog_id;

		if( is_single_product() ) {
			$slides = array();

			if ( shopp('product', 'has-images') ) {
				while( shopp('product','images') ) {
					$pinterest_link = '';
					if ( mg_is_leather_site() ) $pinterest_link = '<a class="single-leather-pinterst-button" href="http://www.pinterest.com/pin/create/button/?url=' . urlencode( shopp('product','get-link') ) . '&media=' . urlencode( shopp('product','get-image',"setting={$args['size']}&property=src") ) . '&description=' . urlencode( shopp('product.get-image','property=title') ) . '" data-pin-do="buttonPin" data-pin-config="none"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>';

					$data = array( 'content' => shopp('product','get-image',"setting={$args['size']}") . '<p class="caption">' . shopp('product.get-image','property=title') . $pinterest_link . '</p>' );
					$slides[] = $data;
				}
			}
		}
		return $slides;
	}

	public static function gallery ( $result, $options, $O ) {
		if ( empty($O->images) ) $O->load_data(array('images'));
		if ( empty($O->images) ) return false;
		$styles = '';
		$_size = 240;
		$_width = shopp_setting('gallery_small_width');
		$_height = shopp_setting('gallery_small_height');

		if (!$_width) $_width = $_size;
		if (!$_height) $_height = $_size;

		$defaults = array(

			// Layout settings
			'margins' => 20,
			'rowthumbs' => false,
			// 'thumbpos' => 'after',

			// Preview image settings
			'p_setting' => false,
			'p_size' => false,
			'p_width' => false,
			'p_height' => false,
			'p_fit' => false,
			'p_sharpen' => false,
			'p_quality' => false,
			'p_bg' => false,
			'p_link' => true,
			'rel' => '',

			// Thumbnail image settings
			'thumbsetting' => false,
			'thumbsize' => false,
			'thumbwidth' => false,
			'thumbheight' => false,
			'thumbfit' => false,
			'thumbsharpen' => false,
			'thumbquality' => false,
			'thumbbg' => false,

			// Effects settings
			'zoomfx' => 'shopp-zoom',
			'preview' => 'click',
			'colorbox' => '{}'


		);

		// Populate defaults from named settings, if provided
		$ImageSettings = ImageSettings::object();

		if (!empty($options['p_setting'])) {
			$settings = $ImageSettings->get( $options['p_setting']);
			if ($settings) $defaults = array_merge($defaults,$settings->options('p_'));
		}

		if (!empty($options['thumbsetting'])) {
			$settings = $ImageSettings->get( $options['thumbsetting']);
			if ($settings) $defaults = array_merge($defaults,$settings->options('thumb'));
		}

		$optionset = array_merge($defaults,$options);

		// Translate dot-notation options to underscore
		$options = array();
		$keys = array_keys($optionset);
		foreach ($keys as $key)
			$options[str_replace('.','_',$key)] = $optionset[$key];

		extract($options);


		if ($p_size > 0)
			$_width = $_height = $p_size;

		$width = $p_width > 0 ? $p_width : $_width;
		$height = $p_height > 0 ? $p_height : $_height;

		$preview_width = $width;

		$previews = '<ul class="previews">';
		$firstPreview = true;

		// Find the max dimensions to use for the preview spacing image
		$maxwidth = $maxheight = 0;

		foreach ($O->images as $img) {
			$scale = $p_fit ? array_search($p_fit, $img->_scaling) : false;
			$scaled = $img->scaled($width, $height, $scale);
			$maxwidth = max($maxwidth, $scaled['width']);
			$maxheight = max($maxheight, $scaled['height']);
		}

		if ($maxwidth == 0) $maxwidth = $width;
		if ($maxheight == 0) $maxheight = $height;

		$p_link = Shopp::str_true($p_link);

		// Setup preview images
		foreach ($O->images as $img) {
			$scale = $p_fit ? array_search($p_fit, $img->_scaling) : false;
			$sharpen = $p_sharpen ? min($p_sharpen, $img->_sharpen) : false;
			$quality = $p_quality ? min($p_quality, $img->_quality) : false;
			$fill = $p_bg ? hexdec(ltrim($p_bg, '#')) : false;
			if ('transparent' == strtolower($p_bg)) $fill = -1;
			$scaled = $img->scaled($width, $height, $scale);

			if ($firstPreview) { // Adds "filler" image to reserve the dimensions in the DOM

				$href = Shopp::url('' != get_option('permalink_structure')?trailingslashit('000'):'000','images');
				$previews .= '<li'.(($firstPreview)?' class="fill"':'').'>';
				$previews .= '<img src="'.add_query_string("$maxwidth,$maxheight",$href).'" alt=" " width="'.$maxwidth.'" height="'.$maxheight.'" />';
				$previews .= '</li>';
			}
			$title = !empty($img->title)?' title="'.esc_attr($img->title).'"':'';
			$alt = esc_attr(!empty($img->alt)?$img->alt:$img->filename);

			$previews .= '<li id="preview-'.$img->id.'"'.(($firstPreview)?' class="active"':'').'>';

            $href = $img->url();

			if ($p_link) $previews .= '<a href="'.$href.'" class="gallery product_'.$O->id.' '.$options['zoomfx'].'"'.(!empty($rel)?' rel="'.$rel.'"':'').''.$title.'>';
			// else $previews .= '<a name="preview-'.$img->id.'">'; // If links are turned off, leave the <a> so we don't break layout
			$previews .= '<img src="'.$img->url($width,$height,$scale,$sharpen,$quality,$fill).'"'.$title.' alt="'.$alt.'" width="'.$scaled['width'].'" height="'.$scaled['height'].'" data-zoom-image="' . $href . '" />';
			if ($p_link) $previews .= '</a>';
			$previews .= '</li>';
			$firstPreview = false;
		}
		$previews .= '</ul>';

		$thumbs = "";
		$twidth = $preview_width+$margins;

		if (count($O->images) > 1) {
			$default_size = 64;
			$_thumbwidth = shopp_setting('gallery_thumbnail_width');
			$_thumbheight = shopp_setting('gallery_thumbnail_height');
			if (!$_thumbwidth) $_thumbwidth = $default_size;
			if (!$_thumbheight) $_thumbheight = $default_size;

			if ($thumbsize > 0) $thumbwidth = $thumbheight = $thumbsize;

			$width = $thumbwidth > 0?$thumbwidth:$_thumbwidth;
			$height = $thumbheight > 0?$thumbheight:$_thumbheight;

			$firstThumb = true;
			$count = 1;
			$thumbs = '<ul class="thumbnails">';
			foreach ($O->images as $img) {
				$scale = $thumbfit?array_search($thumbfit,$img->_scaling):false;
				$sharpen = $thumbsharpen?min($thumbsharpen,$img->_sharpen):false;
				$quality = $thumbquality?min($thumbquality,$img->_quality):false;
				$fill = $thumbbg?hexdec(ltrim($thumbbg,'#')):false;
				if ('transparent' == strtolower($thumbbg)) $fill = -1;

				$scaled = $img->scaled($width,$height,$scale);

				$title = !empty($img->title)?' title="'.esc_attr($img->title).'"':'';
				$alt = esc_attr(!empty($img->alt)?$img->alt:$img->name);

				$thumbs .= '<li id="thumbnail-'.$img->id.'" class="preview-'.$img->id.(($firstThumb)?' first':'').' one-third">';
				$thumbs .= '<img src="'.$img->url($width,$height,$scale,$sharpen,$quality,$fill).'"'.$title.' alt="'.$alt.'" width="'.$scaled['width'].'" height="'.$scaled['height'].'" />';
				$thumbs .= '</li>'."\n";
				$firstThumb = false;

				if($count == 3) {
					$firstThumb = true;
					$count = 0;
				}
				$count++;
			}
			$thumbs .= '</ul>';

		}
		if ($rowthumbs > 0) $twidth = ($width+$margins+2)*(int)$rowthumbs;

		$result = '<div id="gallery-'.$O->id.'" class="gallery">'.$previews.$thumbs.'</div>';
		$script = "\t".'ShoppGallery("#gallery-'.$O->id.'","'.$preview.'");';
		add_storefrontjs($script);

		return $result;
	}

	function zoomgallery($result, $options, $Product) {

		if ( shopp($Product, 'has-images') ) {
			$result = "";

			$small_coverimage = shopp($Product, 'get-coverimage', 'property=url&setting=gallery-previews');
			$large_coverimage = shopp($Product, 'get-coverimage', 'property=url&size=original');
			$initial_label = shopp($Product, 'get-coverimage', 'property=title');
			//$zoom_icon = file_get_contents( get_stylesheet_directory_uri() . '/assets/icons/zoom-in.svg' );
			$pinterest_link = '<a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"  data-pin-color="white"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_white_20.png" /></a><script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>';

			$result .= "<div class='easyzoom easyzoom--adjacent easyzoom--with-thumbnails'><a href='$large_coverimage'><img src='$small_coverimage' /></a></div>";
			$result .= "<p class='image-meta'><span class='label'>$initial_label</span>&nbsp; <span class='pinterest-share'>$pinterest_link</span></p>";
			$result .= "<ul class='thumbnails'>";

			while ( shopp($Product, 'images') ) {
				$small_image = shopp($Product, 'get-image', 'property=url&setting=gallery-previews');
				$large_image = shopp($Product, 'get-image', 'property=url&size=original');
				$thumb_image = shopp($Product, 'get-image', 'property=url&setting=gallery-thumbnails');
				$image_label = shopp($Product, 'get-image', 'property=title');
				$thumb_classes = "thumbnail-" . shopp($Product, 'get-image', 'property=id');

				$result .= "<li><a href='$large_image' data-title='$image_label' data-standard='$small_image' class='$thumb_classes'><img src='$thumb_image' alt='Thumbnails'/></a></li>";
			}

			$result .= "</ul>";
		}

		return $result;
	}

	function zoomgallery_mobile($result, $options, $Product) {
		if ( shopp($Product, 'has-images') ) {

			$count = 0;
			while ( shopp($Product, 'images') ) {
				$count += 1;
			}

			$result = '';

			$result .= '<div class="product-slider-outer-wrap">';
			if ( $count > 1 ) {
				$result .= '<div class="left-arrow"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" id="Layer_1" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><polygon fill="#fff" points="160,115.4 180.7,96 352,256 180.7,416 160,396.7 310.5,256 "/></svg></div>';
				$result .= '<div class="right-arrow"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" id="Layer_1" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><polygon fill="#fff" points="160,115.4 180.7,96 352,256 180.7,416 160,396.7 310.5,256 "/></svg></div>';
			}

			$result .= '<div class="product-slider">';
				while ( shopp($Product, 'images') ) {
					$small_coverimage = shopp($Product, 'get-image', 'property=url&setting=gallery-previews');
					$large_coverimage = shopp($Product, 'get-image', 'property=url&size=original');
					$pinterest_link = '<a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"  data-pin-color="white"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_white_20.png" /></a><script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>';
					$initial_label = shopp($Product, 'get-image', 'property=title');
					$thumb_classes = "thumbnail-" . shopp($Product, 'get-image', 'property=id');

					$result .= "<div>";

					$result .= "<div class='easyzoom easyzoom--adjacent'>";
						$result .= "<a href='$large_coverimage'>";
						$result .= shopp($Product, 'get-image', 'setting=gallery-previews');
						$result .= "</a>";
						$result .= "<p class='image-meta'><span class='label'>$initial_label</span>&nbsp; <span class='pinterest-share'>$pinterest_link</span></p>";
						$result .= "</div>";
					$result .= "</div>";

				}
			$result .= '</div>';
			$result .= '</div>';

			if ( $count > 1 ) {
				$result .= '<div class="product-slider-nav-outer-wrap">';
				if ( $count > 3 ) {
					$result .= '<div class="left-arrow"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" id="Layer_1" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><polygon fill="#fff" points="160,115.4 180.7,96 352,256 180.7,416 160,396.7 310.5,256 "/></svg></div>';
					$result .= '<div class="right-arrow"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" id="Layer_1" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><polygon fill="#fff" points="160,115.4 180.7,96 352,256 180.7,416 160,396.7 310.5,256 "/></svg></div>';
				}

				$result .= '<div class="product-slider-nav">';
				while ( shopp($Product, 'images') ) {
					$thumb_classes = "thumbnail-" . shopp($Product, 'get-image', 'property=id');

					$result .= "<div class='wrapper-{$thumb_classes}'>";
					$result .= shopp($Product, 'get-image', 'setting=gallery-previews&class=' . $thumb_classes);
					$result .= "</div>";
				}
				$result .= '</div>';
				$result .= '</div>';
			} else {
				$result .= '<div class="product-slider-nav-outer-wrap">';
				// add just an image and we can set up styling to 3 wide
				$result .= '</div>';
			}

		}

		return $result;
	}
}

$MG_ShoppGallery = new MG_ShoppGallery();
