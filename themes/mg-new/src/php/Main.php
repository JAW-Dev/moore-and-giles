<?php
namespace Objectiv\Site;

use \Timber\Timber;
use \Timber\Site;

use \Objectiv\Site\Factories\TwigFunctionFactory;
use \Objectiv\Site\Woo\WooSetup;
use Objectiv\Site\Utilities as Utilities;

/**
 * Set up Timber's Site Object
 *
 * @since 1.0
 */
class Main extends Site {

	/**
	 * @var string The parent theme directory
	 */
	private $parent_theme_dir = '';

	/**
	 * @var TwigFunctionFactory Twig functions for use in templates
	 */
	private $twig_functions = null;

	/**
	 * @var WooSetup Setup for woocommerce
	 */
	private $woo_setup = null;

	/**
	 * ObjectivSite constructor.
	 *
	 * @param $parent_theme_dir
	 */
	public function __construct( $parent_theme_dir ) {
		parent::__construct();

		$this->set_parent_theme_dir( $parent_theme_dir );

		// Theme Support
		add_theme_support( 'menus' );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus(
			apply_filters( 'objectiv_site_nav_menus', array() )
		);

		$this->setup();
		$this->load_actions();
		$this->load_filters();
	}

	/**
	 * Be wary of changing the order of these calls unless you know what you are doing. Otherwise bad things and side
	 * effects await for you in the fiery depths of this website
	 */
	public function setup() {
		$this->setup_timber_redirect_filters();
		$this->setup_woo();
		$this->setup_acf();
	}

	/**
	 * Load the actions...
	 */
	public function load_actions() {
		// Enqueue front-end scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Enqueue Overide scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_woo_overide_scripts' ), 999 );

		// Fot fonts and localized scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Initialize Objectiv Widgets
		add_action( 'widgets_init', array( $this, 'load_widgets' ) );

		// Add SVG Sprite to footer
		add_action( 'wp_footer', array( $this, 'add_svg_sprite' ), 9999 );

		// Instantiate class level objects
		add_action( 'wp', array( $this, 'create_main_objects' ) );

		// Hotjar script
		add_action( 'wp_head', array( $this, 'hotjar_script' ) );
	}

	/**
	 * Load the filters...
	 */
	public function load_filters() {
		// Add to Timber Context
		add_filter( 'timber/context', array( $this, 'add_social_to_context' ) );
		add_filter( 'timber/context', array( $this, 'add_is_search_to_context' ) );
		add_filter( 'timber/context', array( $this, 'context_setup' ), 99, 1 );

		if ( $this->is_woo_enabled() ) {
			add_filter( 'timber_context', array( $this, 'add_is_product_category_to_context' ) );
			add_filter( 'timber_context', array( $this, 'add_archive_category_to_context' ) );
			add_filter( 'timber_context', array( $this, 'add_product_features_to_context' ) );
			add_filter( 'timber/context', array( $this, 'add_catalog_options_to_context' ) );
			add_filter( 'timber_context', array( $this, 'add_product_color_to_context' ) );
			add_filter( 'timber/context', array( $this, 'add_woocommerce' ) );
		}

		// Functions to add to Twig
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );

		// Twig extensions
		add_filter( 'timber/twig', array( $this, 'add_extensions_to_twig' ) );

		// Handle page templates
		add_filter( 'theme_page_templates', array( $this, 'add_twig_page_templates' ) );

		// Disable Gutenberg for all post types but 'post'.
		add_filter(
			'use_block_editor_for_post',
			function( $use_block_editor, $type ) {
				$use_block_editor = false;
				if ( 'post' === $type->post_type ) {
					$use_block_editor = true;
				}

				return $use_block_editor;
			},
			10,
			2
		);
	}

	/**
	 * Load the site widgets both custom and placeholders
	 */
	public function load_widgets() {
		register_sidebar(
			array(
				'name' => __( 'Primary Sidebar', 'objectiv' ),
				'id'   => 'primary-sidebar',
			)
		);
	}

	/**
	 * The WooCommerce setup object for our framework
	 */
	public function setup_woo() {
		if ( $this->is_woo_enabled() ) {
			$this->set_woo_setup( new WooSetup() );
		}
	}

	/**
	 * The fonts for the site. Allows for default stack and override
	 */
	public function enqueue_styles() {
		// Main Stylesheet
		wp_enqueue_style( "{$GLOBALS['objectiv_prefix']}_stylesheet", get_stylesheet_uri(), array(), MG_STYLESHEET_VERSION );

		// Fonts
		$fonts = apply_filters( 'objectiv_site_fonts', array() );

		foreach ( $fonts as $handle => $font ) {
			wp_enqueue_style( $handle, $font, array(), MG_STYLESHEET_VERSION );
		}
	}

	/**
	 * NOTE: This is a very important function. This is the brains of the whole Auto Timber template hijacking process.
	 * This process currently is VERY loose and fast. I also have 0 idea of the unintended side effects. HOWEVER, that said
	 * after many many hours of research looking through affiliated code I have come to the personal conclusion that hijacking
	 * it in this way shouldn't bring about armageddon, or any other strange bugs, hopefully...
	 */
	public function setup_timber_redirect_filters() {

		// List of types taken direction from get_query_template
		$types = array(
			'index',
			'404',
			'archive',
			'author',
			'category',
			'tag',
			'taxonomy',
			'date',
			'embed',
			'home',
			'frontpage',
			'page',
			'paged',
			'search',
			'single',
			'singular',
			'attachment',
		);

		// Loop through the types and replace the .php ending with the appropriate view folder / template name .twig
		foreach ( $types as $type ) {
			add_filter(
				"{$type}_template_hierarchy",
				function( $templates ) {
					return array_map(
						function( $template ) {
							$twig_path = Timber::$dirname[0] . '/' . preg_replace( '/.php/', '.twig', $template );

							if ( file_exists( get_stylesheet_directory() . '/' . $twig_path ) ) {
								return $twig_path;
							}

							return $template;
						},
						$templates
					);
				}
			);
		}

		// Now that we have replaced our .php ending we need to bind it to the twig ending and render it.
		add_filter(
			'template_include',
			function( $template ) {
				// Get the context
				$context = Timber::get_context();

				// Context setup generic to all types of pages
				$context['posts']          = apply_filters( 'objectiv_site_posts_filter', new \Timber\PostQuery() );
				$context['post']           = apply_filters( 'objectiv_site_post_filter', Timber::get_post() );
				$context['sidebar']        = apply_filters( 'objectiv_site_widgets_filter', Timber::get_widgets( 'primary-sidebar' ) );
				$context['global_request'] = $_REQUEST;

				// Context for page-list
				if ( strpos( $template, 'template-page-list.twig' ) ) {
					$context['page_list_details'] = $this->get_page_list_template_context();
				}

				$context = apply_filters( 'objectiv_site_timber_template_context', $context );

				// Get the name without it.
				$template_file = pathinfo( $template, PATHINFO_FILENAME );
				$templates     = apply_filters( 'objectiv_site_timber_template_templates', array( $template ) );

				// Add a filter to the site_ts_context to add some default items.
				add_filter(
					'objectiv_site_ts_context',
					function ( $data ) use ( $template_file, $context ) {

						// If we are using WooCommerce get the correct template file js context
						if ( $this->is_woo_enabled() ) {
							$template_file = $this->get_woo_setup()->get_js_template_file( $template_file );
						}

						$data['context']['base']   = $template_file;
						$data['context']['params'] = apply_filters( "objectiv_site_ts_context_{$template_file}_params", array() );

						return $data;
					}
				);

				// If WooCommerce is enabled and we're on the main WooCommerce template page (where everything woo redirects from and so do we)
				if ( $this->is_woo_enabled() && strpos( $template, 'woocommerce.php' ) ) {
					$this->get_woo_setup()->load_context( $context );

					// Otherwise we render the normal WordPress templates
				} else {
					Timber::render( $templates, $context );
				}
			},
			99
		);
	}

	/**
	 * Set up acf and acf options
	 */
	public function setup_acf() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => 'Theme Settings',
					'menu_title' => 'Theme',
					'menu_slug'  => 'theme-general-settings',
					'icon_url'   => 'dashicons-art',
					'capability' => 'edit_posts',
					'position'   => 59.5,
					'redirect'   => false,
				)
			);

			acf_add_options_page(
				array(
					'page_title'      => 'Menu Settings',
					'menu_title'      => 'Menu',
					'menu_slug'       => 'menu_settings',
					'parent_slug'     => 'theme-general-settings',
					'capability'      => 'edit_posts',
					'redirect'        => false,
					'update_button'   => __( 'Update Menu Settings', 'acf' ),
					'updated_message' => __( 'Menu Settings Updated', 'acf' ),
					'post_id'         => 'menu_settings',
				)
			);
		}
	}

	/**
	 *
	 */
	public function create_main_objects() {
		$this->set_twig_functions( new TwigFunctionFactory() );
	}

	public function add_extensions_to_twig( \Twig\Environment $twig ) {
		if ( class_exists( '\\Kint\\Twig\\TwigExtension' ) ) {
			$twig->addExtension( new \Kint\Twig\TwigExtension() );
		}

		return $twig;
	}

	function add_twig_page_templates( $post_templates ) {
		$new_post_templates = $this->get_twig_post_templates();
		$new_post_templates = $new_post_templates + $post_templates;

		return $new_post_templates['page'];
	}

	function add_social_to_context( $context ) {
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}
		$context['social'] = obj_get_acf_field( 'social_links', 'option', true, true );

		return $context;
	}

	function context_setup( $context ) {
		$context['site'] = $this;
		$context['nav']  = array(
			'meta'              => array(
				'menu'  => new Menu( 'site_meta' ),
				'icons' => true,
			),
			'footer_mobile'     => array(
				'menu'        => new Menu( 'site_footer_mobile' ),
				'orientation' => 'vertical',
			),
			'footer_desktop'    => array(
				'menu' => new Menu( 'site_footer_desktop' ),
			),
			'footer_disclaimer' => array(
				'menu' => new Menu( 'site_footer_disclaimer' ),
			),
			'woo_bags'          => array(
				'menu' => new Menu( 'site_woo_bags' ),
			),
			'mobile_bottom'     => array(
				'menu' => new Menu( 'site_mobile_bottom' ),
			),
			'mobile_middle'     => array(
				'menu' => new Menu( 'site_mobile_middle' ),
			),
		);

		$context['footer']['information']        = obj_get_acf_field( 'footer_information', 'option' );
		$context['recent_posts']                 = Timber::get_posts(
			array(
				'posts_per_page' => 3,
			)
		);
		$context['mega_menu']['drop_top']        = obj_get_acf_field( 'dropdown_top_menu', 'menu_settings', true, true );
		$context['mega_menu']['mega_menu_items'] = obj_get_acf_field( 'mega_menu_items', 'menu_settings', true, true );
		$context['top_bar_left_text']            = obj_get_acf_field( 'top_bar_left_text', 'option', true, true );
		$context['meta_menu_cta']['blurb']       = obj_get_acf_field( 'meta_menu_cta_blurb', 'option', true, true );
		$context['meta_menu_cta']['link']        = obj_get_acf_field( 'meta_menu_cta_link', 'option', true, true );

		return $context;
	}

	function add_is_search_to_context( $context ) {
		$context['is_search']    = isset( $_GET['fwp_keywords_filter'] ) ? true : false;
		$context['search_value'] = isset( $_GET['fwp_keywords_filter'] ) ? wp_unslash( sanitize_text_field( $_GET['fwp_keywords_filter'] ) ) : '';

		return $context;
	}

	function add_is_product_category_to_context( $context ) {
		$context['is_product_category'] = is_product_category();
		return $context;
	}

	function add_archive_category_to_context( $context ) {
		global $wp_query;
		$context['product_category'] = $wp_query->get_queried_object();
		return $context;
	}

	function add_product_features_to_context( $context ) {
		global $product;
		if ( is_object( $product ) ) {
			$features      = get_the_terms( $product->get_id(), 'pa_product-features' );
			$list_features = ( ! empty( $features ) ) && isset( $features->name ) ? wp_list_pluck( $features, 'name' ) : array();

			$context['product_features'] = $list_features;
		}
		return $context;
	}

	function add_catalog_options_to_context( $context ) {
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}

		$context['catalog'] = array(
			'quick_shop_button' => obj_get_acf_field( 'woo_quick_shop_button', 'option' ),
		);

		return $context;
	}

	function add_product_color_to_context( $context ) {
		global $product;
		if ( is_object( $product ) ) {
			$color         = get_the_terms( $product->get_id(), 'pa_color' );
			$list_features = ( ! empty( $color[0] ) ) ? $color[0]->name : '';

			$context['product_color'] = $list_features;
		}
		return $context;
	}

	function add_woocommerce( $context ) {
		$context['woocommerce'] = WC();

		return $context;
	}

	/**
	 * Load JS files for the theme
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {
		global $product;

		$file_time = file_exists( get_template_directory() . '/dist/js/site.js' ) ? filemtime( get_template_directory() . '/dist/js/site.js' ) : MG_THEME_VERSION;

		// Register all js
		wp_enqueue_script(
			'objectiv-theme',
			MG_THEME_URI . 'dist/js/site.js',
			array( 'jquery', 'jquery-blockui', 'wc-single-product', 'flexslider', 'wc-add-to-cart-variation', 'wc-cart' ),
			$file_time,
			true
		);

		// Get the datetime for the ONE countdown.
		$one_datetime         = '';
		if ( function_exists( 'get_field' ) ) {
			$one_flexible_content = get_field('mg_flexible_content', get_the_ID());

			if (!empty($one_flexible_content)) {
				foreach ($one_flexible_content as $content) {
					if ($content['acf_fc_layout'] === 'product_image_carousel_and_info') {
						$one_datetime = !empty($content['mg_product_image_carousel_info_product']['count_down_date']) ? $content['mg_product_image_carousel_info_product']['count_down_date'] : '';
					}
				}
			}
		}

		$localize_script_array = array(
			'context'         => null,
			'siteUrl'         => get_bloginfo( 'url' ),
			'siteTitle'       => get_bloginfo( 'name' ),
			'adminAjax'       => admin_url( 'admin-ajax.php' ),
			'variationColors' => Utilities\GetColorVariationData::data(),
			'countDownTime'   => $one_datetime,
		);

		if ( function_exists( 'is_product' ) && is_product() ) {
			$product = wc_get_product( get_the_ID() );

			$localize_script_array['productType'] = $product->get_type();

			$localize_script_array['mgProductCommentData'] = array(
				'adminAjax'           => admin_url( 'admin-ajax.php' ),
				'singleProductID'     => get_the_ID(),
				'commentsCurrentPage' => 1,
				'totalCommentPages'   => ceil( $product->get_review_count() / 5 ),
			);
		}

		/**
		 * Localized here but if you are wondering where context is being set go take a look at around 169 or if this
		 * line is no longer relevant search for objectiv_site_ts_context filter
		 *
		 * TODO: Next website change the variable name mgData to siteData to be site agnostic
		 */
		wp_localize_script( 'objectiv-theme', 'mgData', apply_filters( 'objectiv_site_ts_context', $localize_script_array ) );
	}

	/**
	 * Enqueue Scripts to override core Woocommerce script
	 * This action needs run late (999) to override scripts.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function enqueue_woo_overide_scripts() {
		/**
		 * Dequeue the Woocommerce wc-add-to-cart-variation
		 * Enqueue custom wc-add-to-cart-variation
		 *
		 * @author Jason Witt
		 */

		if ( is_archive() ) {
			if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
				wp_enqueue_script( 'flexslider' );
			}
			wp_enqueue_script( 'wc-single-product' );
		}
	}

	/**
	 * Add custom functions to Twig
	 *
	 * @param object $twig
	 * @return object $twig
	 *
	 * @since 1.0
	 */
	public function add_to_twig( $twig ) {
		return $twig;
	}

	/**
	 * Returns the theme's post templates.
	 *
	 * @since 4.7.0
	 *
	 * @return array Array of page templates, keyed by filename and post type,
	 *               with the value of the translated header name.
	 */
	public function get_twig_post_templates() {
		$post_templates = array();

		$files = (array) $files = (array) self::scandir( trailingslashit( get_stylesheet_directory() ) . 'views', 'twig', 1 );

		foreach ( $files as $file => $full_path ) {
			if ( ! preg_match( '|Template Name:(.*)$|mi', file_get_contents( $full_path ), $header ) ) {
				continue;
			}

			$types = array( 'page' );
			if ( preg_match( '|Template Post Type:(.*)$|mi', file_get_contents( $full_path ), $type ) ) {
				$types = explode( ',', _cleanup_header_comment( $type[1] ) );
			}

			foreach ( $types as $type ) {
				$type = sanitize_key( $type );
				if ( ! isset( $post_templates[ $type ] ) ) {
					$post_templates[ $type ] = array();
				}

				// Switch this back to PHP file extensions because this is what WordPress understands
				// Our template filters will automatically find the twig version and render at runtime
				$post_templates[ $type ][ str_replace( '.twig', '.php', $file ) ] = _cleanup_header_comment( $header[1] );

				wp_cache_add( 'post_templates-mg', $post_templates, 'themes', 1800 );
			}
		}

		return $post_templates;
	}

	/**
	 * Scans a directory for files of a certain extension.
	 *
	 * @since 3.4.0
	 *
	 * @static
	 *
	 * @param string            $path          Absolute path to search.
	 * @param array|string|null $extensions    Optional. Array of extensions to find, string of a single extension,
	 *                                         or null for all extensions. Default null.
	 * @param int               $depth         Optional. How many levels deep to search for files. Accepts 0, 1+, or
	 *                                         -1 (infinite depth). Default 0.
	 * @param string            $relative_path Optional. The basename of the absolute path. Used to control the
	 *                                         returned path for the found files, particularly when this function
	 *                                         recurses to lower depths. Default empty.
	 * @return array|false Array of files, keyed by the path to the file relative to the `$path` directory prepended
	 *                     with `$relative_path`, with the values being absolute paths. False otherwise.
	 */
	private static function scandir( $path, $extensions = null, $depth = 0, $relative_path = '' ) {
		if ( ! is_dir( $path ) ) {
			return false;
		}

		if ( $extensions ) {
			$extensions  = (array) $extensions;
			$_extensions = implode( '|', $extensions );
		}

		$relative_path = trailingslashit( $relative_path );
		if ( '/' == $relative_path ) {
			$relative_path = '';
		}

		$results = scandir( $path );
		$files   = array();

		/**
		 * Filters the array of excluded directories and files while scanning theme folder.
		 *
		 * @since 4.7.4
		 *
		 * @param array $exclusions Array of excluded directories and files.
		 */
		$exclusions = (array) apply_filters( 'theme_scandir_exclusions', array( 'CVS', 'node_modules', 'vendor', 'bower_components' ) );

		foreach ( $results as $result ) {
			if ( '.' == $result[0] || in_array( $result, $exclusions, true ) ) {
				continue;
			}
			if ( is_dir( $path . '/' . $result ) ) {
				if ( ! $depth ) {
					continue;
				}
				$found = self::scandir( $path . '/' . $result, $extensions, $depth - 1, $relative_path . $result );
				$files = array_merge_recursive( $files, $found );
			} elseif ( ! $extensions || preg_match( '~\.(' . $_extensions . ')$~', $result ) ) {
				$files[ $relative_path . $result ] = $path . '/' . $result;
			}
		}

		return $files;
	}

	/**
	 * @return string
	 */
	public function get_parent_theme_dir() {
		return $this->parent_theme_dir;
	}

	/**
	 * @param string $parent_theme_dir
	 */
	public function set_parent_theme_dir( $parent_theme_dir ) {
		$this->parent_theme_dir = $parent_theme_dir;
	}

	/**
	 * @return TwigFunctionFactory
	 */
	public function get_twig_functions() {
		return $this->twig_functions;
	}

	/**
	 * @param TwigFunctionFactory $twig_functions
	 */
	public function set_twig_functions( $twig_functions ) {
		$this->twig_functions = $twig_functions;
	}

	/**
	 * @return bool
	 */
	public function is_woo_enabled() {
		return defined( 'WC_VERSION' );
	}

	/**
	 * @return WooSetup
	 */
	public function get_woo_setup() {
		return $this->woo_setup;
	}

	/**
	 * @param WooSetup $woo_setup
	 */
	public function set_woo_setup( $woo_setup ) {
		$this->woo_setup = $woo_setup;
	}

	public function get_page_list_template_context() {
		$allpageargs    = array(
			'sort_order'   => 'asc',
			'sort_column'  => 'post_title',
			'hierarchical' => 1,
			'child_of'     => 0,
			'parent'       => -1,
			'offset'       => 0,
			'post_type'    => 'page',
			'post_status'  => 'publish,private,draft',
		);
		$pages          = get_pages( $allpageargs );
		$used_templates = array();

		foreach ( $pages as $page ) {
			$id       = $page->ID;
			$template = get_page_template_slug( $id );

			if ( ! empty( $template ) && ! in_array( $template, $used_templates ) ) {
				array_push( $used_templates, $template );
			}
		}

		sort( $used_templates );

		$results = array(
			'all_pages'     => $pages,
			'all_templates' => $used_templates,
		);

		return $results;
	}

	function hotjar_script() {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) || WP_LOCAL_DEV ) {
			return;
		}
		?>
		<!-- Hotjar Tracking Code for www.mooreandgiles.com -->
		<script>
			(function(h,o,t,j,a,r){
				h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
				h._hjSettings={hjid:1818432,hjsv:6};
				a=o.getElementsByTagName('head')[0];
				r=o.createElement('script');r.async=1;
				r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
				a.appendChild(r);
			})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
		</script>

		<!-- Path 2 Response TruConnect Tagging -->
		<img height="1" width="1" style="display:none" src="https://p.alocdn.com/c/u9a98s8n/a/etarget/p.gif?label=mooregiles"/>
		<?php
	}
}
