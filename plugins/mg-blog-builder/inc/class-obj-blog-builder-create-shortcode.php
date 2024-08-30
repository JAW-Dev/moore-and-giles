<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class to create each field type
 */
class OBJ_Blog_Builder_Create_Shortcode {

    public function __construct( $slug, $shortcode, $file ) {

        $this->file = $file;
        $this->slug = $slug;
        $this->shortcode = $shortcode;

        add_shortcode( 'blog_module_' . $this->slug, array( $this, 'create_shortcode' ) );

    }

    public function create_shortcode( $atts ) {
        $template_loader = new OBJ_Blog_Builder_Template_Loader;
        $defaults = array();

        foreach ( $this->shortcode['atts'] as $key => $att ) {
            $defaults[$key] = $att['default'];
        }

		$defaults['slug'] = $this->slug;

        $atts = apply_filters( 'blog_module_' . $this->slug . '_defaults', shortcode_atts( $defaults, $atts ) );

		$template_loader->set_template_data( $atts, 'atts' );

		ob_start();
		$template_loader->get_template_part( $this->slug );
		return ob_get_clean();

    }

}
