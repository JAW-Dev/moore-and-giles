<?php
/**
 * Furniture Specs
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Components\LeatherTemplate;

/**
 * Furniture Specs
 *
 * @author Jason Witt
 */
class FurnitureSpecs {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Render Markup.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public static function render() {
		$title              = obj_get_acf_field( 'mg_custom_furniture_specs_title' );
		$blurb              = obj_get_acf_field( 'mg_custom_furniture_specs_blurb' );
		$dimensions         = get_field( 'mg_custom_furniture_dimensions' );
		$dimensions_title   = obj_get_acf_field( 'mg_custom_furniture_dimensions_title' );
		$details            = get_field( 'mg_custom_furniture_details' );
		$details_title      = obj_get_acf_field( 'mg_custom_furniture_details_title' );
		$product_care       = obj_get_acf_field( 'mg_custom_furniture_product_care' );
		$product_care_title = obj_get_acf_field( 'mg_custom_furniture_product_care_title' );

		?>
		<div class="faq-specs faq-specs-bg-wrap">
			<div class="wrap faq-specs__links-wrap">
				<a href="#leather-sample-modal" id="swatches-label-sample2" class="swatches-label__sample" data-nonce="<?php echo esc_attr( wp_create_nonce( 'leather-sample' ) ); ?>">Leather Details</a>
				<a href="#request-sample-modal" id="request-sample-trigger" class="request-sample__trigger faq-specs__links-wrap-form">Order Swatches</a>
			</div>

			<div class="wrap faq-specs__top-wrap">
				<h3 class="faq-specs__top-wrap-title">
					<?php echo wp_kses_post( $title ); ?>
				</h3>
				<div class="faq-specs__top-wrap-blurb">
					<?php echo wp_kses_post( $blurb ); ?>
				</div>
			</div>

			<div class="wrap faq-specs__specs-wrap">

				<div class="faq-specs__specs-wrap-left">
					<h4 class="faq-specs__specs-wrap-title"><?php echo esc_html( $dimensions_title ); ?></h4>
					<?php foreach ( $dimensions as $dimension ) { ?>
						<div class="faq-specs__specs-wrap-left-wrap">
							<div class="faq-specs__specs-wrap-left-left">
								<?php echo wp_kses_post( $dimension['title'] ); ?>
							</div>
							<div class="faq-specs__specs-wrap-left-right">
								<?php echo wp_kses_post( $dimension['text'] ); ?>
							</div>
						</div>
						<?php
					}
					?>
				</div>

				<div class="faq-specs__specs-wrap-middle">
					<h4 class="faq-specs__specs-wrap-title"><?php echo esc_html( $details_title ); ?></h4>
					<?php foreach ( $details as $detail ) { ?>
						<div class="faq-specs__specs-wrap-middle-wrap">
							<div class="faq-specs__specs-wrap-middle-wrap-left">
								<?php echo wp_kses_post( $detail['title'] ); ?>
							</div>
							<div class="faq-specs__specs-wrap-middle-wrap-right">
								<?php echo wp_kses_post( $detail['blurb'] ); ?>
							</div>
						</div>
						<?php
					}
					?>
				</div>

				<div class="faq-specs__specs-wrap-right">
					<h4 class="faq-specs__specs-wrap-title"><?php echo esc_html( $product_care_title ); ?></h4>
					<?php echo wp_kses_post( $product_care ); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
