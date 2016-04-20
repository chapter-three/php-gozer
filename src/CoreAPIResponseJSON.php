<?php

namespace Gozer\Core;

class CoreAPIResponseJSON implements CoreAPIResponse {
	public function respond($data) {
		header("Content-Type: application/json");
		$json = json_encode($data, JSON_UNESCAPED_UNICODE);
		if ($json === false) {
			echo('JSON encodeing error ' . json_last_error() . ': ' . json_last_error_msg());
			echo('data:');
			var_dump($data);
			exit();
		}
		echo($json);
	}
}