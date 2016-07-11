<?php

namespace Gozer\Core;

class CoreAPIResponseJSON implements CoreAPIResponse {
	public function __construct() {
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
	
	public function respond($data) {
		$json = json_encode($data);
		if ($json === false) {
			header("HTTP/1.0 500 Internal Server Error.");
			$msg = 'JSON encoding error ' . json_last_error() . ': ' . json_last_error_msg();
			echo(json_encode(array("error" => true, "error_code" => 500, "error_msg" => $msg)));
			exit();
		}
		
		echo($json);
		exit();
	}
	
	public function respondError($msg, $code = 500) {
		switch ($code) {
			case 400:
				header("HTTP/1.0 400 Bad Request"); break;
				break;
			case 401:
				header("HTTP/1.0 401 Unauthorized"); break;
				break;
			default:
			case 500:
				header("HTTP/1.0 500 Internal Server Error."); break;
		}
		
		$json = json_encode(array("error" => true, "error_code" => $code, "error_msg" => $msg));
		if ($json === false) {
			header("HTTP/1.0 500 Internal Server Error.");
			$msg = 'JSON encoding error ' . json_last_error() . ': ' . json_last_error_msg();
			echo(json_encode(array("error" => true, "error_code" => 500, "error_msg" => $msg)));
			exit();
		}
		
		echo($json);
		exit();
	}
}