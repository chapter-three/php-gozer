<?php

namespace Gozer\Core;

use \Twig_Loader_Filesystem;
use \Twig_Environment;

/**
 * Base class for all display controllers.
 * 
 * Initializes Twig if a template if provided and automatically calls $this->display if defined in child class.
 *
 * @author Jim McGowen
 *
 */
abstract class CoreController extends Core
{
	/**
	 * @var Twig_Environment $twig
	 */
	protected $twig;
	protected $get;
	protected $post;
	
	/**
	 * Constructor
	 * 
	 * @var $useTwig Boolean
	 *          Set to true if you want to use a twig tempalte.
	 */
	public function __construct()
	{
		$this->get = $this->sanitizeValues($_GET);
		$this->post = $this->sanitizeValues($_POST);
	}

	/**
	 * Helper function for initializing Twig.
	 */
	protected function initTwig() {
		$twigConfig = array(
			'cache' => TWIG_CACHE_DIR
		);
		if (ENV == 'dev') {
			$twigConfig['auto_reload'] = true;
		}

		$loader = new Twig_Loader_Filesystem(TWIG_TEMPLATE_DIR);
		$this->twig = new Twig_Environment($loader, $twigConfig);
	}

	/**
	 * Helper function to scrub an array of values.
	 * 
	 * @param $values array An array of key/value pairs.
	 * @return array
	 */
	protected function sanitizeValues($values) {
		$safeValues = array();
		foreach ($values as $key => $value) {
			// TODO: Sanitize the value (not sure if this is really needed since sanitizing is really context sensitive)
			$safeValues[$key] = $value;
		}
		return $safeValues;
	}
}