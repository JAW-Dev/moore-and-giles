<?php
if( empty( $image = wp_get_attachment_image( $atts->img_id, 'large' ) ) ) {
	$image = "<img src=\"{$atts->img}\" />";
}
?>

<div class="blog-builder-module blog-builder-<?php echo $atts->slug; ?>">
    <figure>
        <?php echo $image; ?>
        <?php if ( $atts->caption ): ?>
            <figcaption><?php echo $atts->caption; ?></figcaption>
        <?php endif; ?>
    </figure>
</div>
