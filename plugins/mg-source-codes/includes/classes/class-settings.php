<?php
/**
 * Settings.
 *
 * @package    MG_Source_Codes
 * @subpackage MG_Source_Codes/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Source_Codes\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Settings' ) ) {

	/**
	 * Settings
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Settings {

		/**
		 * Options.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $options;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version         The plugin version.
		 *     @type string $plugin_dir_url  The plugin directory URL.
		 *     @type string $plugin_dir_path The plugin Directory Path.
		 *     @type array  $options         The plugin options.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->options = $args['options'];
			$this->hooks();
		}

		/**
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'admin_menu', array( $this, 'admin_submenu' ) );
			add_action( 'admin_init', array( $this, 'settings_init' ) );
		}

		/**
		 * Admin Submenu.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function admin_submenu() {
			add_submenu_page(
				'woocommerce',
				__( 'Source Codes', 'moore-and-giles' ),
				__( 'Source Codes', 'moore-and-giles' ),
				'manage_options',
				'mg_source_codes',
				array( $this, 'options_page' )
			);
		}

		/**
		 * Init.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function settings_init() {
			$this->register_settings();
			$this->settings_section();
			$this->settings_fields();
		}

		/**
		 * Register Settings.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function register_settings() {
			register_setting(
				'mg_source_codes_options',
				'mg_source_codes'
			);
		}

		/**
		 * Settings Section.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function settings_section() {
			add_settings_section(
				'mg_source_codes_section',
				'',
				array( $this, 'settings_section_callback' ),
				'mg_source_codes_options'
			);
		}

		/**
		 * Settings Fields.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function settings_fields() {
			add_settings_field(
				'source_codes_list',
				__( 'Source Codes', 'moore-adn-giles' ),
				array( $this, 'source_codes_list_render' ),
				'mg_source_codes_options',
				'mg_source_codes_section'
			);
		}

		/**
		 * TextField.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function source_codes_list_render() {
			$value = isset( $this->options['source_codes_list'] ) ? $this->options['source_codes_list'] : '';
			?>
			<textarea cols='40' rows='20' name='mg_source_codes[source_codes_list]'><?php echo esc_html( $value ); ?></textarea>
			<?php
		}

		/**
		 * Section Callback.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function settings_section_callback() {
			echo esc_html( __( 'Enter each source code one per line', 'moore-adn-giles' ) );
		}

		/**
		 * Options Page.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function options_page() {
			?>
			<form action='options.php' method='post'>
				<h2>Source Codes</h2>
				<?php
				settings_fields( 'mg_source_codes_options' );
				do_settings_sections( 'mg_source_codes_options' );
				submit_button();
				?>
			</form>
			<?php
		}
	}
}
