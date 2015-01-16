<?php

require_once('Core.php');

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
	protected $twig;
	protected $template;
	protected $get;
	protected $post;
	
	/**
	 * Constructor
	 * 
	 * @var $template string
	 *          The name of the template file for this controller (optional).
	 */
	public function __construct($template = null)
	{
		$this->template = $template;
		
		if ($this->template != null) {
			$this->initTwig();
		}
		
		$this->get = $this->scrubValues($_GET);
		$this->post = $this->scrubValues($_POST);
	}

	/**
	 * Helper function for initializing Twig.
	 */
	private function initTwig() {
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
	protected function scrubValues($values) {
		$safeValues = array();
		foreach ($values as $key => $value) {
			// TODO: Sanitize the value (not sure if this is really needed since sanitizing is really context sensitive)
			$safeValues[$key] = $value;
		}
		return $safeValues;
	}
}