<?php

namespace Gozer\Core;

class CoreAPIResponseJSON implements CoreAPIResponse {
	public function respond($data) {
		header("Content-Type: application/json");
		$json = json_encode($data);
		echo($json);
	}
}