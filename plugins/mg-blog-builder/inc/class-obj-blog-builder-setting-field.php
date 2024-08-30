<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class to create each field type
 */
class OBJ_Blog_Builder_Setting_Field {

    public function __construct( $slug, $atts ) {

        $this->slug = $slug;
        $this->atts = $atts;
        $this->create_field();

    }

    public function create_field() {
        $slug = $this->slug;
        $atts = $this->atts;

        if ( $atts ) {

            $html = '';

            foreach( $atts as $attr_name => $attr_info ) {

                $html .= '<p id="blog-module-' . $slug . '-' . $attr_name . '" class="blog-module-setting-field">';
                $html .= '<label for="blog-module-attr-' . $attr_name . '">' . $attr_info['desc'] . '</label>';

                if ( $attr_info['type'] == 'media_upload' ) {
                    $html .= '<input type="text" name="' . $attr_name . '" value="" id="blog-module-attr-' . $attr_name . '" class="widefat blog-module-attr" />';
                    $html .= '<input id="blog-module-upload-img" type="button" class="button button-secondary blog-module-upload-button" value="Select Media" />';
                }

                if ( $attr_info['type'] == 'text' ) {
                    $html .= '<input type="text" id="blog-module-attr-' . $attr_name . '" name="' . $attr_name . '" value="" class="widefat blog-module-attr" />';
                }

                if ( $attr_info['type'] == 'textarea' ) {
                    $html .= '<textarea name="' . $attr_name . '" id="blog-module-attr-' . $attr_name . '" rows="6" class="widefat blog-module-attr"></textarea>';
                }

                $html .= '</p>';

            }

            echo $html;

        }

    }

}
