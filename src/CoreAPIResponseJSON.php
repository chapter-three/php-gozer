<?php

namespace Gozer\Core;

class CoreAPIResponseJSON implements CoreAPIResponse {
	public function respond($data) {
		echo(json_encode($data));
		exit();
	}
}