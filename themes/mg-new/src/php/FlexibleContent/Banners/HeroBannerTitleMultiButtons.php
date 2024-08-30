<?php
/**
 * HeroBannerTitleMultiButtons
 *
 * @package    Package_Name
 * @subpackage Package_Name/Subpackage_Name
 * @author     Author_Name
 * @copyright  Copyright (c) Date, Author_Name
 * @license    GPL-2.0
 * @version    1.0.0
 * @since      1.0.0
 */

namespace Objectiv\Site\FlexibleContent\Banners;

use Objectiv\Site\Utilities as Utilities;
use Objectiv\Site\FlexibleContent\Utils\SectionBackground;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * HeroBannerTitleMultiButtons
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class HeroBannerTitleMultiButtons {

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
	 * @param array $content The Field Content.
	 *
	 * @return void
	 */
	public function template( $content ) {
		$background    = $content['mg_section_background'];
		$section_class = 'title-multi-buttons';

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
		$style        = ! empty( $content['style'] ) ? ' ' . $content['style'] : '';
		$buttons      = ! empty( $content['buttons'] ) ? $content['buttons'] : array();
		$button_count = count( $buttons );
		$title        = ! empty( $content['title'] ) ? $content['title'] : '';

		?>
		<div class="wrap<?php echo esc_attr( $style ); ?>">
			<div class="title-multi-buttons__header-body">
				<?php if ( ! empty( $title ) ) : ?>
					<h2 class="title-multi-buttons__header-body-title">
						<?php echo wp_kses_post( $title ); ?>
					</h2>
				<?php endif; ?>

				<?php if ( ! empty( $buttons ) ) : ?>
					<div class="title-multi-buttons__header-body-buttons">
						<?php
						for ( $i = 0; $i < $button_count; $i++ ) {
							$button       = $buttons[ $i ]['button'];
							$url          = $button['url'];
							$target       = $button['target'];
							$title        = $button['title'];
							$button_style = $buttons[ $i ]['button_style'];

							?>
							<a href="<?php echo esc_url( $url ); ?>" target="<?php echo esc_attr( $target ); ?>" class="button <?php echo esc_attr( $button_style ); ?>">
								<?php echo wp_kses_post( $title ); ?>
							</a>
							<?php
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
