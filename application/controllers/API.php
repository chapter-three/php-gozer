<?php

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
	public function testAction($param1, $param2) {
		$payload = array(
			'text' => 'this is a test',
			'params' => array($param1, $param2)
		);
		
		echo $this->encodeJson($payload);
	}
}