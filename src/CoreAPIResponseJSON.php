<?php

namespace Gozer\Core;

class CoreAPIResponseJSON implements CoreAPIResponse {
	public function respond($data) {
		try {
			header("Content-Type: application/json");
			$json = json_encode($data);
			echo($json);
		}
		catch(\Exception $e) {
			$blah = 0;
		}
	}
}