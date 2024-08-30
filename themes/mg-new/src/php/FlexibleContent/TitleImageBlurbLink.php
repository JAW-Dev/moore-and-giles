<?php
/**
 * Title Image Blurb Link
 *
 * @package    Package_Name
 * @subpackage Package_Name/Subpackage_Name
 * @author     Author_Name
 * @copyright  Copyright (c) Date, Author_Name
 * @license    GPL-2.0
 * @version    1.0.0
 * @since      1.0.0
 */

namespace Objectiv\Site\FlexibleContent;

use Objectiv\Site\FlexibleContent\Utils\SectionBackground;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Title Image Blurb Link
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class TitleImageBlurbLink {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Template
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $content The content.
	 *
	 * @return void
	 */
	public function template( $content ) {
		$background     = $content['mg_section_background'];
		$custom_classes = $content['mg_section_classes'] ? ' ' . $content['mg_section_classes'] : '';
		$section_class  = 'title-image-blurb-link' . $custom_classes;

		SectionBackground::start( $background, $section_class );
		$this->body( $content );
		SectionBackground::end( $background );
	}

	/**
	 * Body
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $content The content args.
	 *
	 * @return void
	 */
	public function body( $content ) {
		$title       = $content['title'];
		$blurb       = $content['blurb'];
		$image       = ! empty( wp_get_attachment_image_src( $content['image'], 'medium' ) ) ? wp_get_attachment_image_src( $content['image'], 'medium' )[0]: '';
		$link        = ! empty( $content['link'] ) ? $content['link'] : array();
		$link_url    = ! empty( $link['url'] ) ? $link['url'] : '';
		$link_target = ! empty( $link['target'] ) ? $link['target'] : '';
		$link_title  = ! empty( $link['title'] ) ? $link['title'] : '';

		?>
		<div class="wrap">
			<?php if ( ! empty( $title ) ) : ?>
				<h3 class="title-image-blurb-link__heading">
					<?php echo wp_kses_post( $title ); ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $image ) ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" class="title-image-blurb-link__image">
			<?php endif; ?>

			<?php if ( ! empty( $blurb ) ) : ?>
				<div class="title-image-blurb-link__blurb">
					<?php echo wp_kses_post( $blurb ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $link ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="title-image-blurb-link__link" target="<?php echo esc_attr( $link_target ); ?>">
					<?php echo wp_kses_post( $link_title ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
