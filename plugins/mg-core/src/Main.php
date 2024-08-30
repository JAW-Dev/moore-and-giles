<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 */

namespace MG_Core;
use MG_Core\Admin\Controller;
use Symfony\Component\Finder\Finder;


/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @author     Your Name <email@example.com>
 */
class Main extends \WordPress_SimpleSettings {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      \MG_Core\utils\Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;


	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;


	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $available_modules    The available modules.
	 */
	protected $available_modules;

	/**
	 * @return array
	 */
	public function get_available_modules() {
		return $this->available_modules;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->prefix = '_mg_core';
		parent::__construct();

		$this->plugin_name = 'mg-core';
		$this->version = '1.0.0';
		$this->loader = new utils\Loader();

		$this->set_locale();
		$this->load_modules();
		$this->run_modules();
		$this->set_public_hooks();
		$this->set_admin_hooks();
	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Internationalization class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new utils\Internationalization();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	function load_modules() {
		$Modules = new Finder();

		$Modules->files()->in(__DIR__ . '/Modules/')->name('*.php');

		foreach ( $Modules as $module ) {
			// Don't try to load the base class
			if ( $module->getFilename() == "Base.php" ) continue;

			$class_name = str_replace('.php', '', $module->getRelativePathname());
			$class_name = str_replace('/', '\\', $class_name);
			$class_name = '\\MG_Core\\Modules\\' . $class_name;

			$Object = new $class_name();

			$this->available_modules[ $Object->get_id() ] = $Object;
		}
	}

	function run_modules() {
		$module_states = $this->get_setting( 'module_states' );

		if ( $module_states ) {
			foreach ( $module_states as $module_id => $enabled_state ) {
				if ( $enabled_state !== "yes" ) continue;

				if ( ! empty( $this->available_modules[ $module_id ] ) ) {
					$this->available_modules[ $module_id ]->run();
				}
			}
		}
	}

	function set_public_hooks() {

	}

	function set_admin_hooks() {
		$plugin_admin = new Controller( $this );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    \MG_Core\utils\Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
