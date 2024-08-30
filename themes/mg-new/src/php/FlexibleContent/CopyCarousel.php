<?php
/**
 * Copy Carousel
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
 * Copy Carousel
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class CopyCarousel {

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
		$background    = $content['mg_section_background'];
		$section_class = 'copy-carousel';

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
		$copy   = ! empty( $content['copy'] ) ? $content['copy'] : array();
		$link   = ! empty( $content['link'] ) ? $content['link'] : array();
		$url    = ! empty( $link['url'] ) ? $link['url'] : '';
		$target = ! empty( $link['target'] ) ? $link['target'] : '';
		$title   = ! empty( $link['title'] ) ? $link['title'] : '';
		?>
		<div class="wrap">
			<?php if ( ! empty( $copy ) ) : ?>
				<div class="copy-carousel__selector">
					<?php
					foreach ( $copy as $item ) {
						$text = $item['button_text'];
						?>
						<div class="copy-carousel__selector-item">
							<?php echo esc_html( $text ); ?>
						</div>
						<?php
					}
					?>
				</div>
				<div class="copy-carousel__carousel">
					<?php
					foreach ( $copy as $item ) {
						$text = $item['body_text'];
						?>
						<div class="copy-carousel__carousel-item">
							<?php echo wp_kses_post( $text ); ?>
						</div>
						<?php
					}
					?>
				</div>
			<?php endif; ?>

			<?php if ( $link ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="copy-carousel__link" target="<?php echo esc_attr( $target ); ?>">
					<?php echo wp_kses_post( $title ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
