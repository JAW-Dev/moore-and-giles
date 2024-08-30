<?php
/**
 * Upcoming Bags
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

use Objectiv\Site\Utilities as Utilities;
use Objectiv\Site\FlexibleContent\Utils\SectionBackground;
use Objectiv\Site\TwigFunctions\SvgTwigFunction;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Upcoming Bags
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class UpcomingBags {

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
		$section_class = 'upcoming-bags';

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
		$email_blurb = ! empty( $content['email_blurb'] ) ? $content['email_blurb'] : '';
		$this->carousel( $content );
	}

	/**
	 * Carousel
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $content The content.
	 *
	 * @return void
	 */
	public function carousel( $content ) {
		$title        = ! empty( $content['title'] ) ? $content['title'] : '';
		$sub_title    = ! empty( $content['sub_title'] ) ? $content['sub_title'] : '';
		$product_name = ! empty( $content['product_name'] ) ? $content['product_name'] : '';
		$images       = ! empty( $content['images'] ) ? $content['images'] : array();
		$email_blurb  = ! empty( $content['email_blurb'] ) ? $content['email_blurb'] : '';

		?>
		<div class="wrap">
			<?php if ( ! empty( $title ) ) : ?>
				<h3 class="upcoming-bags__heading">
					<?php echo wp_kses_post( $title ); ?>
				</h3>
			<?php endif; ?>

			<div class="upcoming-bags__images">
				<?php
				if ( ! empty( $images ) ) {
					for ( $i = 0; $i < count( $images ); $i++ ) {
						$img_src = wp_get_attachment_image_src( $images[ $i ]['image'] );
						$url     = ! empty( $img_src[0] ) ? $img_src[0] : '';
						$class   = ' upcoming-bags__other-images';

						if ( $i === 0 ) {
							$class = ' upcoming-bags__first-image';
						}

						if ( ! empty( $url ) ) {
							?>
							<div class="upcoming-bags__image<?php echo esc_attr( $class ); ?>">
								<?php
								if ( $i !== 0 ) {
									?>
									<div class="upcoming-bags__image-overlay"></div>
									<?php
								}
								?>
								<img src="<?php echo esc_url( $url ); ?>" />
							</div>
							<?php
						}
					}
				}
				?>
			</div>
			<div class="upcoming-bags__bottom">
				<div class="upcoming-bags__bottom-left">

					<?php if ( ! empty( $sub_title ) ): ?>
						<h4 class="upcoming-bags__bottom-title">
							<?php echo wp_kses_post( $sub_title ); ?>
						</h4>
					<?php endif; ?>

					<?php if ( ! empty( $product_name ) ): ?>
						<h5 class="upcoming-bags__product-name">
							<?php echo wp_kses_post( $product_name ); ?>
						</h5>
					<?php endif; ?>
				</div>
				<div class="upcoming-bags__bottom-right">

					<?php if ( ! empty( $email_blurb ) ): ?>
						<div class="upcoming-bags__email-blurb">
							<?php echo wp_kses_post( $email_blurb ); ?>
						</div>
					<?php endif; ?>

					<?php
					$action = obj_get_acf_field( 'one_form_action', get_the_ID() ) ? obj_get_acf_field( 'one_form_action', get_the_ID() ) : 'https://customers.listrak.com/q/GZYIVKsXTpi30E30PN2bSXmIklD-KgJZxW';
					$value  = obj_get_acf_field( 'one_form_crvs_value', get_the_ID() ) ? obj_get_acf_field( 'one_form_crvs_value', get_the_ID() ) : 'AlaRohSLa8DrPgqwitVkl4uZKgQ_91IYPGacpPynFDA4jaAEZOkdMVsfhHt8MnNE2BOzwaSIU0DvXWJ3EDGfxMKomZX2AdnqHjuvOX_08ASoo3nSC-uvSbNhu-FV0lsoshVG4tUrNx2jCIiWC6rqUMYecZowxRdQGyz798zw6P89JgKSkrV2uuQiUs2WmJco';
					?>

					<form method="post" action="<?php echo esc_attr( $action ); ?>" accept-charset="UTF-8">
						<span class="mg-input-wrap">
							<input type="hidden" name="crvs" value="<?php echo esc_attr( $value ); ?>">
							<input type="text" class="mg-input" name="email" size="40" maxlength="100" value="" placeholder="Email" required="">
							<input type="hidden" name="CheckBox.Source.Footer" value="on">
							<span class="validation-wrap" style="display: none;">
								<?php
								$svg = new SvgTwigFunction( 'validated' );
								echo $svg->action( 'validated' );
								?>
							</span>
						</span>
						<input type="submit" class="button button__orange" id="submit" value="Sign Up">
					</form>
				</div>
			</div>
		</div>
		<?php

	}
}
