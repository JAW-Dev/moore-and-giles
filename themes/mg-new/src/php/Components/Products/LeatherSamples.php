<?php
/**
 * Leather Sample
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Components\Products;

/**
 * Leather Sample
 *
 * @author Jason Witt
 */
class LeatherSamples {

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
	public function render() {
		?>
		<div class="about-the-leather active">
			<div id="about-the-leather-image" class="about-the-leather__image"></div>
			<div class="about-the-leather__content">
				<div class="about-the-leather__content-inner lmb0 fmt0">
					<h4 class="about-the-leather__content-title margin-bot-1 ff-proxima ">About <span id="about-the-leather-title"></span></h4>
					<div class="about-the-leather__content-blurb margin-bot-1 ff-proxima" ><span id="about-the-leather-content"></span></div>
					<div class="about-the-leather__content-footer">
						<div class="about-the-leather__footer-blurb">Feel the difference for yourself. Request a complimentary swatch.</div>
						<a href="#swatch-form" class="button button__ghost about-the-leather__button request-sample__trigger">Request Swatch</a>
					</div>
				</div>
			</div>
			<div id="swatch-form" class="about-the-leather__form" style="display: none;">
				<?php echo do_shortcode( '[gravityform id="' . mg_get_leater_swatch_form_id() . '" ajax="true" title="true" description="true"]' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Leather Initial Ajax
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public static function leather_sample_ajax() {
		echo wp_json_encode( self::leather_info() );
		exit;
	}

	/**
	 * Leather Info
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public static function leather_info() {
		$sample_name = ! empty( $_POST['sample_name'] ) ? sanitize_text_field( wp_unslash( $_POST['sample_name'] ) ) : '';
		$sample_slug = ! empty( $_POST['sample_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['sample_slug'] ) ) : '';
		$product_id  = ! empty( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$colors      = get_the_terms( $product_id, 'pa_color' );
		$data        = array();

		$leather_pages = get_posts(
			array(
				'post_type'      => 'leather_information',
				'posts_per_page' => -1,
			)
		);

		foreach ( $leather_pages as $leather_page ) {
			foreach ( $colors as $color ) {
				if ( $color->slug === $sample_slug ) {
					$color_name = $color->name;

					if ( stripos( $color_name, $leather_page->post_title ) !== false ) {
						$image_id        = get_term_meta( $color->term_id, 'product_attribute_image', true );
						$image_data      = wp_get_attachment_image_src( $image_id );
						$data['id']      = $color->slug;
						$data['name']    = $color->slug;
						$data['image']   = ! empty( $image_data[0] ) ? $image_data[0] : '';
						$data['title']   = $leather_page->post_title;
						$data['content'] = $leather_page->post_content;
					}
				}
			}
		}

		return $data;
	}
}
