<?php
/*
Plugin Name: MG API
Plugin URI: https://bitbucket.org/cgdinc/mooreandgiles
Description: WP plugin for building MG's custom API
Author: Wes Cole
Version: 0.1
Author URI: http://objectiv.co
*/

class MG_Api
{
    public function init()
    {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes()
    {
        register_rest_route( 'mg/v1', '/supreme', array(
            'methods' => 'GET',
            'callback' => array( $this, 'supreme_callback' ),
        ) );
    }

    public function supreme_callback()
    {
        switch_to_blog(3);

        $prefix = "_mg_";
        $id = 10127; // Staging
        // $id = 9877; // local
        $data = array();

        $color = array();
        $color['title'] = get_post_meta( $id, $prefix . 'cs_color_title', true );
        $color['description'] = get_post_meta( $id, $prefix . 'cs_color_description', true );
        $color['moodImages'] = get_post_meta( $id, $prefix . 'cs_mood_images_group', true );

        $grain = array();
        $grain['title'] = get_post_meta( $id, $prefix . 'cs_grain_title', true );
        $grain['description'] = get_post_meta( $id, $prefix . 'cs_grain_description', true );
        $grain['grains'] = get_post_meta( $id, $prefix . 'cs_grains_group', true );

        $finish = array();
        $finish['title'] = get_post_meta( $id, $prefix . 'cs_finish_title', true );
        $finish['description'] = get_post_meta( $id, $prefix . 'cs_finish_description', true );
        $finish['finishes'] = get_post_meta( $id, $prefix . 'cs_finishes_group', true );

        $progress = array();
        $progress['title'] = get_post_meta( $id, $prefix . 'cs_progress_title', true );
        $progress['description'] = get_post_meta( $id, $prefix . 'cs_progress_description', true );

        $data['color'] = $color;
        $data['grain'] = $grain;
        $data['finish'] = $finish;
        $data['progress'] = $progress;

        restore_current_blog();
        return $data;
    }
}

$api = new MG_Api();
$api->init();
