<?php
class CS_Fields
{
    public function custom_fields()
    {
        $prefix = "_mg_";

        if (function_exists( 'new_cmb2_box' )) {
            $content = new_cmb2_box( array(
                'id'    => $prefix . 'cs_content',
                'title' => 'Page Content',
                'object_types'  => array( 'page' ),
                'show_on'      => array( 'key' => 'id', 'value' => array( 10127 ) ),
                'content'   => 'normal',
                'priority'  => 'high',
                'show_names'    => true
            ) );

            $content->add_field( array(
                'name'  => 'Top Title',
                'id'    => $prefix . 'cs_content_top_title',
                'type'  => 'text'
            ) );

            $content->add_field( array(
                'name'  => 'Bottom Title',
                'id'    => $prefix . 'cs_content_bottom_title',
                'type'  => 'text'
            ) );

            $content->add_field( array(
                'name'  => 'Page Description',
                'id'    => $prefix . 'cs_content_description',
                'type'  => 'textarea'
            ) );

            $content->add_field( array(
                'name'  => 'Steps Title',
                'id'    => $prefix . 'cs_content_steps_title',
                'type'  => 'text'
            ) );

            $icons = $content->add_field(array(
                'id'          => $prefix . 'cs_content_icons',
                'type'        => 'group',
                'options'     => array(
                    'group_title'   => __( 'Icon {#}', 'cmb2' ),
                    'add_button'    => __( 'Add Another Icon', 'cmb2' ),
                    'remove_button' => __( 'Remove Icon', 'cmb2' ),
                    'sortable'      => true,
                ),
            ) );

            $content->add_group_field( $icons, array(
                'id'    => 'icon',
                'name'  => 'Icon',
                'type'  => 'select',
                'show_option_none' => false,
                'default'   => 'aniline',
                'options'   => array(
                    'aniline_icon'   => '2-3 Week Lead Time',
                    'hides_icon' => '2 Hide Minimum',
                    'semianiline_icon'  => 'Semi-Aniline Finished Leather',
                    'scratch_icon'   => 'Scratch Resistant',
                    'size_icon' => 'Hide Size',
                    'flag_icon' => 'Flag'
                )
            ) );

            $content->add_group_field( $icons, array(
                'id'    => 'content',
                'name'  => 'Content',
                'type'  => 'textarea'
            ) );

            $color = new_cmb2_box( array(
                'id'    => $prefix . 'cs_color',
                'title' => 'Color',
                'object_types'  => array( 'page' ),
                'show_on'      => array( 'key' => 'id', 'value' => array( 10127 ) ),
                'content'   => 'normal',
                'priority'  => 'high',
                'show_names'    => true
            ) );

            $color->add_field( array(
                'name'  => 'Title',
                'id'    => $prefix . 'cs_color_title',
                'type'  => 'text'
            ) );

            $color->add_field( array(
                'name'  => 'Description',
                'id'    => $prefix . 'cs_color_description',
                'type'  => 'textarea'
            ) );

            $images = $color->add_field(array(
                'id'          => $prefix . 'cs_mood_images_group',
                'type'        => 'group',
                'options'     => array(
                    'group_title'   => __( 'Image {#}', 'cmb2' ),
                    'add_button'    => __( 'Add Another Image', 'cmb2' ),
                    'remove_button' => __( 'Remove Image', 'cmb2' ),
                    'sortable'      => true,
                ),
            ) );

            $color->add_group_field( $images, array(
                'name'  => 'Image',
                'id'    => 'image',
                'type'  => 'file'
            ) );

            $grains = new_cmb2_box( array(
                'id'    => $prefix . 'cs_grains',
                'title' => 'Grains',
                'object_types'  => array( 'page' ),
                'show_on'      => array( 'key' => 'id', 'value' => array( 10127 ) ),
                'content'   => 'normal',
                'priority'  => 'high',
                'show_names'    => true
            ) );

            $grains->add_field( array(
                'name'  => 'Title',
                'id'    => $prefix . 'cs_grain_title',
                'type'  => 'text'
            ) );

            $grains->add_field( array(
                'name'  => 'Description',
                'id'    => $prefix . 'cs_grain_description',
                'type'  => 'textarea'
            ) );

            $grain = $grains->add_field(array(
                'id'          => $prefix . 'cs_grains_group',
                'type'        => 'group',
                'options'     => array(
                    'group_title'   => __( 'Grain {#}', 'cmb2' ),
                    'add_button'    => __( 'Add Another Grain', 'cmb2' ),
                    'remove_button' => __( 'Remove Grain', 'cmb2' ),
                    'sortable'      => true,
                ),
            ) );

            $grains->add_group_field( $grain, array(
                'name'  => 'Grain Image',
                'id'    => 'image',
                'type'  => 'file'
            ) );

            $grains->add_group_field( $grain, array(
                'name' => 'Grain Name',
                'id'    => 'name',
                'type'  => 'text'
            ) );

            $grains->add_group_field( $grain, array(
                'name'  => 'Grain Description',
                'id'    => 'description',
                'type'  => 'textarea'
            ) );

            $finishes = new_cmb2_box( array(
                'id'    => $prefix . 'cs_finishes',
                'title' => 'Finishes',
                'object_types'  => array( 'page' ),
                'show_on'      => array( 'key' => 'id', 'value' => array( 10127 ) ),
                'content'   => 'normal',
                'priority'  => 'high',
                'show_names'    => true
            ) );

            $finishes->add_field( array(
                'name'  => 'Title',
                'id'    => $prefix . 'cs_finish_title',
                'type'  => 'text'
            ) );

            $finishes->add_field( array(
                'name'  => 'Description',
                'id'    => $prefix . 'cs_finish_description',
                'type'  => 'textarea'
            ) );

            $finish = $finishes->add_field(array(
                'id'          => $prefix . 'cs_finishes_group',
                'type'        => 'group',
                'options'     => array(
                    'group_title'   => __( 'Finish {#}', 'cmb2' ),
                    'add_button'    => __( 'Add Another Finish', 'cmb2' ),
                    'remove_button' => __( 'Remove Finish', 'cmb2' ),
                    'sortable'      => true,
                ),
            ) );

            $finishes->add_group_field( $finish, array(
                'name'  => 'Finish Image',
                'id'    => 'image',
                'type'  => 'file'
            ) );

            $finishes->add_group_field( $finish, array(
                'name' => 'Finish Name',
                'id'    => 'name',
                'type'  => 'text'
            ) );

            $finishes->add_group_field( $finish, array(
                'name'  => 'Finish Description',
                'id'    => 'description',
                'type'  => 'textarea'
            ) );

            $progress = new_cmb2_box( array(
                'id'    => $prefix . 'cs_progress',
                'title' => 'Progress',
                'object_types'  => array( 'page' ),
                'show_on'      => array( 'key' => 'id', 'value' => array( 10127 ) ),
                'content'   => 'normal',
                'priority'  => 'high',
                'show_names'    => true
            ) );

            $progress->add_field( array(
                'id'    => $prefix . 'cs_progress_title',
                'name'  => 'Title',
                'type'  => 'text'
            ) );

            $progress->add_field( array(
                'id'    => $prefix . 'cs_progress_description',
                'name'  => 'Description',
                'type'  => 'textarea'
            ) );
        }
    }
}
