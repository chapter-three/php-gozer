<?php

namespace Gozer\Core;

interface CoreAPIResponse {
	/**
	 * @param $data An array.
	 *
	 * @return mixed
	 */
	public function respond($data);
	
	/**
	 * @param $msg
	 * @param $code
	 *
	 * @return mixed
	 */
	public function respondError($msg, $code = 500);
}