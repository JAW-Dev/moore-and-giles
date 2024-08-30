<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * Old blub for reference
 * Based on <?php echo esc_html( $review_count ); ?> Reviews <?php echo esc_html( $average * 20 ); ?>% of customers recommended this item.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

$rating = intval( get_post_meta( $product->get_id(), '_wc_average_rating', true ) );
$rating = (int) round( $rating );

if ( $rating_count > 0 ) : ?>

	<div class="woocommerce-product-rating">
		<?php echo wc_get_rating_html( $rating, $rating_count ); ?>
		<?php if ( comments_open() ) : ?>
			<span class="count">(<?php echo esc_html( $review_count ); ?>)</span>
		<?php endif ?>
	</div>
	<div class="mobile-average-blurb-wrap">
		<span class="mobile-average-blurb"><?php echo esc_html( $review_count ); ?> reviewers rate this product an average of <?php echo $product->get_average_rating(); ?> out of 5 stars.</span>
	</div>

<?php endif; ?>
