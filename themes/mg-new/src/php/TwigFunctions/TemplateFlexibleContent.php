<?php
/**
 * Template Flexible Content
 *
 * @package Objectiv
 * @subpackage Objectiv/Site/TwigFunctions
 */

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;
use \Objectiv\Site\Factories\TwigFunctionFactory;
use Objectiv\Site\FlexibleContent as FlexibleContent;

/**
 * Template Flexible Content
 */
class TemplateFlexibleContent extends TwigFunction {

	/**
	 * Content
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $content;

	/**
	 * Action
	 *
	 * @param array $field The arguments.
	 *
	 * @return void
	 */
	public function action( $field = 'mg_flexible_content' ) {
		$post_id       = isset( $args['post_id'] ) ? $args['post_id'] : get_the_ID();
		$this->content = function_exists( 'get_field' ) ? get_field( $field, $post_id ) : array();
		$this->get_section_templates();
	}

	/**
	 * Get Section Templates
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function get_section_templates() {
		foreach ( $this->content as $content ) {
			// TODO: REMOVE!
			// error_log( ': ' . print_r( $content, true ) ); // phpcs:ignore
			switch ( $content['acf_fc_layout'] ) {
				case 'hero_banner_title_multi_buttons':
					$section = new FlexibleContent\Banners\HeroBannerTitleMultiButtons();
					$section->template( $content['mg_banner_title_multi_buttons'] );
					break;
				case 'product_image_carousel_and_info':
					$section = new FlexibleContent\ProductImageCarouselAndInfo();
					$section->template( $content['mg_product_image_carousel_info_product'] );
					break;
				case 'fifty_fifty':
					$section = new FlexibleContent\FiftyFifty();
					$section->template( $content );
					break;
				case 'copy_carousel':
					$section = new FlexibleContent\CopyCarousel();
					$section->template( $content['mg_copy_carousel'] );
					break;
				case 'upcoming_bags':
					$section = new FlexibleContent\UpcomingBags();
					$section->template( $content['mg_upcoming_bags'] );
					break;
				case 'title_image_blurb_link':
					$section = new FlexibleContent\TitleImageBlurbLink();
					$section->template( $content['mg_title_image_blurb_link'] );
					break;
			}
		}
	}
}
