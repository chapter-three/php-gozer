<?php

use \Gozer\Core\CoreController;

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
		
		$util = new Util();
		
		// Create a new message and save to db
		$message = new Message();
		$message->setText('blah');
		$em = $this->getEntityManager();
		$em->persist($message);
		//$em->flush();
		
		echo $this->twig->render($this->template, $this->templateVars);
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