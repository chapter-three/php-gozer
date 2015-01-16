<?php

/**
 * Base class for all api controllers.
 * 
 * Automatically calls $this->respond if defined in child class.
 *
 * @author Jim McGowen
 *
 */
abstract class CoreAPI extends Core
{
	/**
	 * Encodes an array of key value pairs into json.
	 * 
	 * @param $payload
	 *
	 * @return string
	 */
	protected function encodeJson($payload) {
		return json_encode($payload);
	}
}