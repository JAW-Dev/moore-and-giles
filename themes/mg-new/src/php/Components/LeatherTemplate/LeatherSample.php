<?php
/**
 * Leather Sample
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Components\LeatherTemplate;

/**
 * Leather Sample
 *
 * @author Jason Witt
 */
class LeatherSample {

	/**
	 * Enabled Varaiations
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected static $enabled_variations;

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {
		global $product;

		self::$enabled_variations = $product->get_available_variations();
	}

	/**
	 * Swatch Fields
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $field_content The field output.
	 * @param object $field         The field object.
	 *
	 * @return string
	 */
	public static function swatch_fields( $field_content, $field ) {
		if ( $field->type === 'checkbox' ) {
			$colors        = get_the_terms( get_the_ID(), 'pa_color' );
			$field_content = str_replace( '</label>', '</label><div class="faux-swatch"></div>', $field_content );

			if ( ! empty( $colors ) ) {
				foreach ( $colors as $color ) {
					$image_id      = get_term_meta( $color->term_id, 'product_attribute_image', true );
					$image_data    = wp_get_attachment_image_src( $image_id );
					$image_src     = ! empty( $image_data[0] ) ? $image_data[0] : '';
					$original      = "value='" . $color->name . "'";
					$new           = "value='" . $color->name . "' data-value='" . $color->slug . "' data-wvstooltip='" . $color->name . "' data-src='" . $image_src . "'";
					$field_content = str_replace( $original, $new, $field_content );
				}
			}
		}

		return $field_content;
	}

	/**
	 * Populate the swatches on form submit.
	 *
	 * The swatches needs to populated again on
	 * submitting the form to pass along the
	 * slected swatched to the form entry.
	 *
	 * @param object $form The form object.
	 *
	 * @return object;
	 */
	public static function submbition_swatches( $form ) {
		return self::populate_sample_checkboxes( $form );
	}

	/**
	 * Populate Sample Checkboxes
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $form The form fields.
	 *
	 * @return array
	 */
	public static function populate_sample_checkboxes( $form ) {

		if ( empty( $form['fields'] ) ) {
			return $form;
		}

		global $product;

		$get_colors = get_the_terms( get_the_ID(), 'pa_color' );
		$colors     = array();

		if ( ! empty( $product ) || is_admin() ) {
			foreach ( self::$enabled_variations as $variation ) {
				$attribute_pa_color = $variation['attributes']['attribute_pa_color'];

				foreach ( $get_colors as $color ) {
					if ( $color->slug === $attribute_pa_color ) {
						$colors[] = $color;
					}
				}
			}
		}

		if ( empty( $colors ) ) {
			$colors = $get_colors;
		}

		foreach ( $form['fields'] as &$field ) {

			$field_id = 6;
			if ( $field->id != $field_id ) {
				continue;
			}

			$input_id = 1;
			foreach ( $colors as $color ) {

				if ( $input_id % 10 == 0 ) {
					$input_id++;
				}

				$choices[] = array(
					'text'  => $color->name,
					'value' => $color->name,
				);

				$inputs[] = array(
					'label' => $color->name,
					'id'    => "{$field_id}.{$input_id}",
				);

				$input_id++;
			}

			$field->choices = $choices;
			$field->inputs  = $inputs;

		}

		return $form;
	}

	/**
	 * Get Sample Data
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public static function get_sample_data() {
		$nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'leather-sample' ) ) {
			exit;
		}

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

		echo wp_json_encode( $data );
		exit;
	}

	/**
	 * Render Info Modal.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public static function render_info_modal() {
		?>
		<div id="leather-sample-modal" class="leather-sample-modal" style="display: none">
			<div src="" id="leather-sample-modal-image" class="leather-sample-modal__image"></div>
			<div class="leather-sample-modal__wrap">
				<h3 id="leather-sample-modal-title" class="leather-sample-modal__title"></h3>
				<div id="leather-sample-modal-body" class="leather-sample-modal__body"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Info Block.
	 *
	 * @author Jason Witt
	 *
	 * @param string $attribute_name The Attribute name.
	 *
	 * @return void
	 */
	public static function render_info_block( $attribute_name ) {
		?>
		<div class="swatches-label">
			<div class="swatches-label__text">
				<?php echo wp_kses_post( self::render_selected_text( $attribute_name ) ); ?>
			</div>
			<a href="#leather-sample-modal" id="swatches-label-sample" class="swatches-label__sample" data-nonce="<?php echo esc_attr( wp_create_nonce( 'leather-sample' ) ); ?>"></a>
		</div>
		<?php
	}

	/**
	 * Render Selected Text
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $attribute_name The Attribute name.
	 *
	 * @return string
	 */
	public static function render_selected_text( $attribute_name ) {
		return 'Selected: <span class="selected-' . $attribute_name . '"></span>';
	}

	/**
	 * Render Form block
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public static function render_form_block() {
		?>
		<div class="request-sample">
			<p class="request-sample__text">Request Free Swatches Arrives in 4-7 business days</p>
			<a href="#request-sample-modal" id="request-sample-trigger" class="request-sample__trigger">Order Swatches</a>
		</div>
		<?php
	}

	/**
	 * Render Form Modal
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public static function render_form_modal() {
		$form_id = mg_get_leater_swatch_form_id();

		?>
		<div id="request-sample-modal" class="request-sample-modal" style="display: none;">
			<?php echo do_shortcode( '[gravityform id="' . $form_id . '" ajax="true" title="true" description="true"]' ); ?>
		</div>
		<?php
	}

	/**
	 * Render Footer
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $button The button output.
	 * @param array  $form   The form array.
	 *
	 * @return string
	 */
	public static function render_footer( $button, $form ) {
		ob_start();
		?>
		<div class="gform_footer__wrap">
			<div id="gform_footer-left" class="gform_footer__left">
				<div id="swatch-choices" class="swatch-choices">
					<h4 class="gform_footer__choices-heading">Your Swatches</h4>
					<div id="gform_footer-choices-wrap" class="gform_footer__choices-wrap">
						<ul id="gform-footer-choices" class="gform_footer__choices"></ul>
						<ul id="gform-footer-choices2" class="gform_footer__choices"></ul>
					</div>
				</div>
			</div>
			<div class="gform_footer__right">
				<p class="gform_footer__right-text">You should receive your</br>swatches in 4-7 business days</p>
				<input type='submit' id='gform_submit_button_<?php echo esc_attr( $form['id'] ); ?>' class='gform_button button button__orange' value='Submit'  onclick='if(window["gf_submitting_<?php echo esc_attr( $form['id'] ); ?>"]){return false;}  window["gf_submitting_<?php echo esc_attr( $form['id'] ); ?>"]=true;  ' onkeypress='if( event.keyCode == 13 ){ if(window["gf_submitting_<?php echo esc_attr( $form['id'] ); ?>"]){return false;} window["gf_submitting_<?php echo esc_attr( $form['id'] ); ?>"]=true;  jQuery("#gform_39").trigger("submit",[true]); }' />
			</div>
		</div>
		<?php

		return ob_get_clean();
	}
}
