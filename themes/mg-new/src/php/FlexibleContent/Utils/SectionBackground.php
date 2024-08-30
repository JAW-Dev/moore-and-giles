<?php
/**
 * Section Background
 *
 * @package    Package_Name
 * @subpackage Package_Name/Subpackage_Name
 * @author     Author_Name
 * @copyright  Copyright (c) Date, Author_Name
 * @license    GPL-2.0
 * @version    1.0.0
 * @since      1.0.0
 */

namespace Objectiv\Site\FlexibleContent\Utils;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Section Background
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class SectionBackground {

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
	 * Start
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array  $background       The background args.
	 * @param string $section_class    The section class.
	 * @param string $video_attributes The video attributes.
	 *
	 * @return void
	 */
	public static function start( $background, $section_class = '', $section_id = '', $video_attributes = 'background=1&autoplay=1&loop=1&title=0&byline=0&portrait=0' ) {
		$background_color = ! empty( $background['background_color'] ) ? 'background-color: ' . $background['background_color'] . ';' : '';
		$background_type  = ! empty( $background['background']['type'] ) ? $background['background']['type'] : '';
		$background_image = ! empty( wp_get_attachment_image_src( $background['background']['image'], 'full' ) ) ? 'background-image: url(' . wp_get_attachment_image_src( $background['background']['image'], 'full' )[0] . ');' : '';
		$video_id         = ! empty( $background['background']['video_id'] ) ? $background['background']['video_id'] : '';
		$overlay          = ! empty( $background['overlay'] ) ? $background['overlay'] : '';

		if ( 'image' === $background_type ) {
			?>
			<section id="<?php echo esc_attr( $section_id ); ?>" class="section section-background <?php echo esc_attr( $section_class ); ?>" style="<?php echo esc_html( $background_color ) . esc_html( $background_image ); ?>">
			<?php
		} elseif ( 'video' === $background_type) {
			?>
			<section id="<?php echo esc_attr( $section_id ); ?>" class="section section-background section-background__video <?php echo esc_attr( $section_class ); ?>" style="<?php echo esc_html( $background_color ); ?>">
				<div class="video">
					<iframe src="https://player.vimeo.com/video/<?php echo esc_attr( $video_id ) . '?' . esc_attr( $video_attributes ); ?>" frameborder="0"></iframe>
				</div>
				<?php
			add_action(
				'wp_enqueue_scripts',
				function() {
					wp_enqueue_script( 'mg-vimeo-player', 'https://player.vimeo.com/api/player.js', array(), '1.0.0', true );
				}
			);
		} else {
			?>
			<section id="<?php echo esc_attr( $section_id ); ?>" class="section section-background <?php echo esc_attr( $section_class ); ?>" style="<?php echo esc_html( $background_color ); ?>">
			<?php
		}

		if ( $overlay['enable'] ) {
			$color   = ! empty( $overlay['color'] ) ? 'background-color: ' . $overlay['color'] . ';' : '';
			$opacity = ! empty( $overlay['opacity'] ) ? 'opacity: ' . $overlay['opacity'] . ';' : '';
			?>
			<div class="section-background__overlay" style="<?php echo esc_html( $color ) . esc_html( $opacity ); ?>"></div>
			<?php
		}
}

	/**
	 * End
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $background The background args.
	 *
	 * @return void
	 */
	public static function end( $background ) {
		$background_type = ! empty( $background['background']['type'] ) ? $background['background']['type'] : '';

		if ( 'video' === $background_type ) {
			?>
			</section>
			<?php
		} else {
			?>
			</section>
			<?php
		}
	}
}
