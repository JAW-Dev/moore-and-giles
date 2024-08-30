<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/review-meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $comment;
$verified = wc_review_is_from_verified_owner( $comment->comment_ID );

use \Objectiv\Site\TwigFunctions\SvgTwigFunction;

// Same twig function we use in the templates. Just create, then call action with the name of the svg file without the .svg extension (see line 43)
$svg = new SvgTwigFunction( 'svg' );

if ( '0' === $comment->comment_approved ) { ?>

	<p class="meta">
		<em class="woocommerce-review__awaiting-approval">
			<?php esc_html_e( 'Your review is awaiting approval', 'woocommerce' ); ?>
		</em>
	</p>

<?php } else { ?>

	<div class="meta">
        <div class="owner-rating-wrap">
		    <h5 class="woocommerce-review__author"><?php comment_author(); ?> </h5>
            <?php wc_get_template('single-product/review-rating.php'); ?>
        </div>
        <div class="verified-owner-wrap">
            <?php
                if ( 'yes' === get_option( 'woocommerce_review_rating_verification_label' ) && $verified ) {
                    echo '<span class="circle-check">' . $svg->action('circle-check') . '</span>';
                    echo '<span class="woocommerce-review__verified verified">' . esc_attr__( 'Verified Buyer', 'woocommerce' ) . '</span> ';
                }
            ?>
        </div>
	</div>

<?php
}
