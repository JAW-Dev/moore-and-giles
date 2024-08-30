<?php
/**
 * Special Offer Banner
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * Specisl Offer Banner
 *
 * @author Jason Witt
 */
class SpecialOfferBanner {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Render.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public static function render() {
		$message = obj_get_acf_field( 'mg_special_offer_banner', 'option' );

		if ( $message ) {
			?>
			<style type="text/css">
				#breadcrumbs {
					margin-top: 10px;
				}
			</style>
			<div class="site-below-menu-bar" role="banner">
				<div class="wrap">
					<?php echo wp_kses_post( $message ); ?>
				</div>
			</div>
			<?php
		}
	}
}
