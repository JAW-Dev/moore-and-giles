<?php
if( empty( $image_1 = wp_get_attachment_image( $atts->img_1_id, 'medium' ) ) ) {
    $image_1 = "<img src=\"{$atts->img_1}\" />";
}

if( empty( $image_2 = wp_get_attachment_image( $atts->img_2_id, 'medium' ) ) ) {
	$image_2 = "<img src=\"{$atts->img_2}\" />";
}
?>
<div class="blog-builder-module blog-builder-<?php echo $atts->slug; ?>">
    <div class="blog-builder-module-grid">
        <div class="blog-builder-image three-four">
            <?php echo $image_1; ?>
        </div>
        <div class="blog-builder-image three-four">
            <?php echo $image_2; ?>
        </div>
    </div>
</div>
