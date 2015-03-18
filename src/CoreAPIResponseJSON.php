<?php

namespace Gozer\Core;

class CoreAPIResponseJSON implements CoreAPIResponse {
	public function respond($data) {
		header("Content-Type: application/json");
		echo(json_encode($data));
		exit();
	}
}