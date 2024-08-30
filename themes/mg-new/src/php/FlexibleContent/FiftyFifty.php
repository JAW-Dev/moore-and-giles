<?php
/**
 * Fifty Fifty
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
 * Fifty Fifty
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class FiftyFifty {

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
		$section_id    = ! empty( $content['mg_section_id'] ) ? $content['mg_section_id'] : '';
		$section_class = 'fifty-fifty';

		SectionBackground::start( $background, $section_class, $section_id );
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
		$title          = $content['title'];
		$blurb          = $content['blurb'];
		$image          = ! empty( wp_get_attachment_image_src( $content['image'], 'medium' ) ) ? wp_get_attachment_image_src( $content['image'], 'medium' )[0]: '';
		$image_position = $content['image_position'];
		$button         = $content['button'];

		?>
		<div class="wrap <?php echo esc_attr( $image_position ); ?>">

			<?php if ( ! empty( $image ) ) : ?>
				<div style="height: 100%" class="fifty-fifty__image-wrap">
					<img src="<?php echo esc_url( $image ); ?>" class="fifty-fifty__image">
				</div>
			<?php endif; ?>

			<div class="fifty-fifty__body">

				<?php if ( ! empty( $title ) ) : ?>
					<h3 class="fifty-fifty__heading">
						<?php echo wp_kses_post( $title ); ?>
					</h3>
				<?php endif; ?>

				<?php if ( ! empty( $blurb ) ) : ?>
					<div class="fifty-fifty__blurb">
						<?php echo wp_kses_post( $blurb ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $button ) ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" class="button button__orange" target="<?php esc_attr( $button['target'] ); ?>">
						<?php echo wp_kses_post( $button['title'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
