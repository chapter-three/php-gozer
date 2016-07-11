<?php

namespace Gozer\Core;

class CoreAPIResponseDefault implements CoreAPIResponse {
	public function respond($data) {
		if ($data === "success") {
			return true;
		}
		return $data;
	}
	
	public function respondError($msg, $code = 500) {
		throw new \Exception($msg, $code);
	}
}