<?php

namespace Objectiv\Site\Factories;

use Objectiv\Site\TwigFunctions\IsNewProductTwigFunction;
use \Objectiv\Site\TwigFunctions\SvgTwigFunction;
use \Objectiv\Site\TwigFunctions\DoShortcodeTwigFunction;
use Objectiv\Site\TwigFunctions\WCPrintNoticesTwigFunction;
use Objectiv\Site\TwigFunctions\GetSimilarProductsTwigFunction;
use Objectiv\Site\TwigFunctions\ErrorLogPrintTwigFunction;
use Objectiv\Site\TwigFunctions\GetAlsoInProductsTwigFunction;
use Objectiv\Site\TwigFunctions\BreadCrumbsTwigFunction;
use Objectiv\Site\TwigFunctions\YearTwigFunction;
use Objectiv\Site\TwigFunctions\BlogNameTwigFunction;
use Objectiv\Site\TwigFunctions\IsProductCategoryTerm;
use Objectiv\Site\TwigFunctions\IsCartOpenedTwigFunction;
use Objectiv\Site\TwigFunctions\GetFlexibleContentTwigFunction;
use Objectiv\Site\TwigFunctions\GetColorAttributesTwigFunction;
use Objectiv\Site\TwigFunctions\SvgUrlTwigFunction;
use Objectiv\Site\TwigFunctions\TeaseProductImagesTwigFunction;
use Objectiv\Site\TwigFunctions\TemplateFlexibleContent;
use Objectiv\Site\TwigFunctions\InitFurnitureSpecsTwigFunction;
use Objectiv\Site\TwigFunctions\InitClassTwigFunction;
use \Timber\Twig_Function;
use \Twig\Environment;

class TwigFunctionFactory {

	/**
	 * @var Twig_Environment
	 */
	private $twig = null;

	/**
	 * @var array
	 */
	private $functions = [];

	/**
	 * @var Twig_Environment
	 */
	private static $env = null;

	/**
	 * TwigFunctionFactory constructor.
	 */
	public function __construct() {
		$this->set_functions(
			apply_filters(
				'objectiv_site_twig_functions',
				[
					new SvgTwigFunction( 'svg' ),
					new IsNewProductTwigFunction( 'is_new_product' ),
					new DoShortcodeTwigFunction( 'do_shortcode' ),
					new WCPrintNoticesTwigFunction( 'wc_print_notices' ),
					new GetSimilarProductsTwigFunction( 'get_similar_products' ),
					new ErrorLogPrintTwigFunction( 'error_log_print' ),
					new GetAlsoInProductsTwigFunction( 'get_also_in_products' ),
					new BreadCrumbsTwigFunction( 'breadcrumbs' ),
					new YearTwigFunction( 'year' ),
					new BlogNameTwigFunction( 'bloginfo_name' ),
					new IsProductCategoryTerm( 'is_product_category_term' ),
					new IsCartOpenedTwigFunction( 'is_cart_opened' ),
					new GetFlexibleContentTwigFunction( 'get_flexible_content' ),
					new GetColorAttributesTwigFunction( 'get_color_attributes' ),
					new SvgUrlTwigFunction( 'svg_url' ),
					new TeaseProductImagesTwigFunction( 'tease_product_images' ),
					new TemplateFlexibleContent( 'template_flexible_content' ),
					new InitFurnitureSpecsTwigFunction( 'custom_furniture_specs' ),
					new InitClassTwigFunction( 'init_class' ),
				]
			)
		);

		add_filter( 'timber/twig', array( $this, 'add_functions_to_twig' ) );
	}

	/**
	 * @param Twig_Environment $twig
	 *
	 * @return mixed
	 */
	function add_functions_to_twig( \Twig\Environment $twig) {
		TwigFunctionFactory::set_env($twig);

		foreach($this->get_functions() as $function) {
			$twig->addFunction(new Twig_Function($function->get_function_name(), [$function, 'action']));
		}

		return apply_filters('objectiv_site_twig_functions_last_chance', $twig);
	}

	/**
	 * @return Twig_Environment
	 */
	public function get_twig() {
		return $this->twig;
	}

	/**
	 * @param Twig_Environment $twig
	 */
	public function set_twig( $twig ) {
		$this->twig = $twig;
	}

	/**
	 * @return array
	 */
	public function get_functions() {
		return $this->functions;
	}

	/**
	 * @param array $functions
	 */
	public function set_functions( $functions ) {
		$this->functions = $functions;
	}

	/**
	 * @return Twig_Environment
	 */
	public static function get_env() {
		return self::$env;
	}

	/**
	 * @param Twig_Environment $env
	 */
	public static function set_env( $env ) {
		self::$env = $env;
	}
}
