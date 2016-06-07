<?php

namespace Gozer\Core;

class CoreAPIResponseJSON implements CoreAPIResponse {
	public function respond($data) {
		header("Content-Type: application/json");
		$json = json_encode($data);
		if ($json === false) {
			header("HTTP/1.0 500 Internal Server Error.");
			$msg = 'JSON encoding error ' . json_last_error() . ': ' . json_last_error_msg();
			echo(json_encode(array("error" => true, "error_code" => 500, "error_msg" => $msg)));
			exit();
		}
		echo($json);
	}
}