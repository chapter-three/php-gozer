<?php

require_once('CoreAPI.php');

/**
 * Class API
 * 
 * Sample webservice api.
 */
class API extends CoreAPI 
{
	/**
	 * Default action.
	 * Called if no action is specified in the route.
	 */
	public function defaultAction() 
	{
		$response = array(
			'something' => array()
		);
		
		echo $this->encodeJson($response);
	}

	/**
	 * Another sample action
	 */
	public function testAction() {
		$payload = array(
			'text' => 'this is a test'
		);
		
		echo $this->encodeJson($payload);
	}
}