<?php

namespace Gozer\Core;

interface CoreAPIResponse {
	/**
	 * @param $data An array.
	 *
	 * @return mixed
	 */
	public function respond($data);
}