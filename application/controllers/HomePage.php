<?php

require_once('CoreController.php');

/**
 * Class HomePage
 * 
 * Sample controller for the home or index page.
 */
class HomePage extends CoreController
{
	/**
	 * defaultAction is called if no action is defined in the route.
	 */
	public function defaultAction() {
		$this->templateVars['title'] = 'PHP Gozer';
		
		$em = $this->getEntityManager();
		
		$html = $this->twig->render($this->template, $this->templateVars);
		echo $html;
	}

	/**
	 * Sample action "adminAction" as defined in routes.json
	 */
	public function adminAction() {
		echo('Administration');
		if (isset($this->get['var'])) {
			echo($this->get['var']);
		}
	}
}