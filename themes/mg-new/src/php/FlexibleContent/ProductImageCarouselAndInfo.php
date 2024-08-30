<?php
/**
 * Product Image Carousel and Info
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

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Product Image Carousel and Info
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class ProductImageCarouselAndInfo {

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
		$section_class = 'product-image-carousel-info';
		$section_id    = ! empty( $content['mg_section_id'] ) ? $content['mg_section_id'] : '';

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
		?>
		<div class="wrap">
			<?php if ( ! empty( $content['title'] ) ) : ?>
				<h3 class="product-image-carousel-info__heading">
					<?php echo wp_kses_post( $content['title'] ); ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $content['product_name'] ) ) : ?>
				<h4 class="product-image-carousel-info__product-name">
					<?php echo wp_kses_post( $content['product_name'] ); ?>
				</h4>
			<?php endif; ?>

			<?php if ( ! empty( $content['short_description'] ) ) : ?>
				<p class="product-image-carousel-info__description">
					<?php echo wp_kses_post( $content['short_description'] ); ?>
				</p>
			<?php endif; ?>

			<?php
			$count_down_date = ! empty( $content['count_down_date'] ) ? $content['count_down_date'] : '';
			$timezone        = 'America/New_York';
			$timestamp       = time();
			$datetime        = new \DateTime( 'now', new \DateTimeZone( $timezone ) );

			$datetime->setTimestamp( $timestamp );

			if ( $datetime->format( 'Y-m-d H:i:s' ) < $count_down_date ) :
				?>
				<div id="product-image-carousel-info-timer" class="product-image-carousel-info__timer">
					<div class="product-image-carousel-info__timer-item">
						<div id="timer-days" class="days product-image-carousel-info__timer-item-num"></div>
						<div class="product-image-carousel-info__timer-item-type">days</div>
					</div>

					<div class="product-image-carousel-info__timer-item">
						<div id="timer-hours" class="hours product-image-carousel-info__timer-item-num"></div>
						<div class="product-image-carousel-info__timer-item-type">hrs</div>
					</div>

					<div class="product-image-carousel-info__timer-item">
						<div id="timer-min" class="min product-image-carousel-info__timer-item-num"></div>
						<div class="product-image-carousel-info__timer-item-type">mins</div>
					</div>

					<div class="product-image-carousel-info__timer-item">
						<div id="timer-sec" class="sec product-image-carousel-info__timer-item-num"></div>
						<div class="product-image-carousel-info__timer-item-type">sec</div>
					</div>
				</div>
			<?php endif; ?>

			<?php
			$images = ! empty( $content['images'] ) ? $content['images'] : array();
			$button = ! empty( $content['button'] ) ? $content['button'] : array();

			$this->carousel( $images, $button );

			$main_description = ! empty( $content['main_description'] ) ? $content['main_description'] : '';
			$features         = ! empty( $content['features'] ) ? $content['features'] : array();
			$dimensions       = ! empty( $content['dimensions'] ) ? $content['dimensions'] : array();

			$this->specs( $main_description, $features, $dimensions, $button );
			?>
		</div>
		<?php
	}

	/**
	 * Carousel
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param WC_Procuct $product The Product.
	 * @param array      $button The button.
	 *
	 * @return void
	 */
	public function carousel( $images = array(), $button = array() ) {

		if ( ! empty( $images ) ) {
			?>
			<div class="product-image-carousel-info__carousel-container">
				<div class="product-image-carousel-info__carousel">
					<?php
					foreach ( $images as $image ) {
						$img_src = wp_get_attachment_image_src( $image['image'], 'woocommerce_single' );
						$url     = ! empty( $img_src[0] ) ? $img_src[0] : '';
						?>
						<div><img src="<?php echo esc_url( $url ); ?>"></div>
					<?php } ?>
				</div>
			</div>
			<div class="product-image-carousel-info__carousel-menu-wrap">

				<?php if ( ! empty( $images ) ) : ?>
					<div class="product-image-carousel-info__carousel-menu">
						<?php
						foreach ( $images as $thumb ) {
							$img_src = wp_get_attachment_image_src( $thumb['image'], 'woocommerce_single' );
							$url     = ! empty( $img_src[0] ) ? $img_src[0] : '';
							?>
							<img src="<?php echo esc_url( $url ); ?>" class="product-image-carousel-info__carousel-menu-image">
						<?php } ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $button ) ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" class="button button__orange button-top" target="<?php echo esc_attr( $button['target'] ); ?>">
						<?php echo esc_html( $button['title'] ); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php
		}

	}

	/**
	 * Specs
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param WC_Procuct $product The Product.
	 *
	 * @return void
	 */
	public function specs( $description = '', $features = array(), $dimensions = array(), $button = array() ) {
		?>
		<div class="product-image-carousel-info__specs">

			<?php if ( ! empty( $description ) ) : ?>
				<div class="product-image-carousel-info__specs-left">
					<p><?php echo wp_kses_post( $description ); ?></p>
				</div>
			<?php endif; ?>


			<div class="product-image-carousel-info__specs-right">

				<?php if ( ! empty( $features ) ): ?>
					<div class="product-image-carousel-info__features">
						<h5><?php echo wp_kses_post( $features['title'] ); ?></h5>
						<?php
						foreach ( $features['feature'] as $feature ) {
							?>
								<p class="product-image-carousel-info__feature"><?php echo wp_kses_post( $feature['text'] ); ?></p>
							<?php
						}
						?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $dimensions ) ): ?>
					<div class="product-image-carousel-info__dimensions">
						<h5><?php echo wp_kses_post( $dimensions['title'] ); ?></h5>
						<?php echo wp_kses_post( $dimensions['blurb'] ); ?>
					</div>
				<?php endif; ?>

			</div>

			<?php if ( ! empty( $button ) ) : ?>
				<a href="<?php echo esc_url( $button['url'] ); ?>" class="button button__orange button-bottom" target="<?php echo esc_attr( $button['target'] ); ?>">
					<?php echo esc_html( $button['title'] ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
