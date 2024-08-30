<?php
/*
Plugin Name: MG Custom Supreme
Plugin URI: https://bitbucket.org/cgdinc/mg-custom-supreme
Description: WP Plugin for creating the Custom Supreme page for Moore and Giles
Author: Wes Cole
Version: 0.1
Author URI: http://objectiv.co
*/

define( 'CS_PATH', plugin_dir_path( __FILE__ ) );
define( 'CS_URL', plugin_dir_url( __FILE__ ) );
require_once( CS_PATH . 'inc/Fields.php' );

class MG_Custom_Supreme
{
    public $plugin_domain;
    public $views_dir;
    public $version;
    public $page_slug;

    public function __construct()
    {
        $this->plugin_domain = 'mg-custom-supreme';
        $this->views_dir = trailingslashit( dirname( __FILE__ ) ) . 'server/views';
        $this->version = '1.2';
        $this->page_slug = 'custom-supreme';
    }

    public function init()
    {
        add_filter( 'page_template', array( $this, 'custom_page_template' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'load_bundle') );
        add_action( 'cmb2_admin_init', array( 'CS_Fields', 'custom_fields' ) );
    }

    public function custom_page_template($page_template)
    {
        if (is_page($this->page_slug)) {
            $page_template = $this->load_view("custom-supreme.php");
        }
        return $page_template;
    }

    public function load_view($view)
    {
        $path = trailingslashit( $this->views_dir ) . $view;
        if (file_exists( $path )) {
            return $path;
        }
    }

    public function load_bundle()
    {
        if (is_page($this->page_slug)) {
            wp_enqueue_script( $this->plugin_domain . '-bundle', plugin_dir_url( __FILE__ ) . 'dist/bundle.js', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_domain . '-css', plugin_dir_url( __FILE__ ) . 'server/dist/style.css', array(), $this->version );
        }
    }
}

$custom_supreme = new MG_Custom_Supreme();
$custom_supreme->init();
