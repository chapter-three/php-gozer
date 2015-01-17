<?php

use Gozer\Core\CoreAPI;

/**
 * Class API
 * 
 * Sample webservice api.
 */
class API extends CoreAPI 
{
	public function __construct() {
		parent::__construct(true);
	}
	
	/**
	 * Default action.
	 * Called if no action is specified in the route.
	 */
	public function defaultAction() 
	{
		echo json_encode(array('success' => true, 'message' => 'You accessed my APIs!'));
		
		/*
		$response = array(
			'something' => array()
		);
		
		echo $this->encodeJson($response);
		*/
	}

	/**
	 * Another sample action
	 */
	public function testAction($param1, $param2) {
		$payload = array(
			'text' => 'this is a test',
			'params' => array($param1, $param2)
		);
		
		echo $this->encodeJson($payload);
	}
}