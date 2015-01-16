<?php

/**
 * This is the bootstrap entry point for all requests.
 * Requires that all requests be routed to this file. (ie. in an .htaccess file).
 */

require_once('../../vendor/autoload.php');

// *** Parse the request URL and load the appropriate page ***
if (is_file(ROUTES_FILE)) {
	$pages = json_decode(file_get_contents(ROUTES_FILE), true);
	if (!$pages) {
		echo "<h1>Error: Invalid routes file.</h1>";
		echo "The routes.json file could not be parsed.";
		exit();
	}

	$reqUrl = $_SERVER['REQUEST_URI'];
	$query = '?' . $_SERVER['QUERY_STRING'];
	$reqUrl = str_replace($query, '', $reqUrl);
	if ($reqUrl != '/' && substr($reqUrl, -1) == '/') {
		$reqUrl = substr($reqUrl, 0, strlen($reqUrl) - 1);
	}

	foreach ($pages as $page) {
		
		$paramCount = 0;
		$urlPattern = $page['url'];
		if (strstr($urlPattern, '%')) {
			$paramCount = substr_count($urlPattern, '%');
			$urlPattern = substr($urlPattern, 0, strpos($urlPattern, '%') - 1);
		}
		
		if ($paramCount > 0 && substr_count($reqUrl, '/') < $paramCount) {
			continue;
		}
		
		$params = array();
		for ($i = 0; $i <= $paramCount - 1; $i++) {
			$params[] = substr($reqUrl, strrpos($reqUrl, '/') + 1);
			$reqUrl = substr($reqUrl, 0, strrpos($reqUrl, '/'));
		}
		$params = array_reverse($params);
		
		if ($urlPattern == $reqUrl) {
			if (!empty($page['redirect'])) {
				header('Location:' . $page['redirect']);
				exit();
			}

			$controller = null;
			if (!empty($page['controller'])) {
				if (!is_file(BASE_PATH . '/application/controllers/' . $page['controller'] . '.php')) {
					die("Controller '" . BASE_PATH . "/application/controllers/{$page['controller']} for page '{$page->url}' could not be found.");
				}
				
				$className = '';
				if (strstr($page['controller'], '/')) {
					//$className = str_replace( '.php', '', substr( $page[ 'controller' ], strrpos( $page[ 'controller' ], '/' ) + 1 ) );
					$className = substr($page['controller'], strrpos($page['controller'], '/') + 1);
				}
				else {
					//$className = str_replace( '.php', '', $page[ 'controller' ] );
					$className = $page['controller'];
				}

				if (isset($page['view'])) {
					$controller = new $className($page['view']);
				}
				else {
					// The controller does not have a view (most likely an API controler)
					$controller = new $className();
				}

				if (isset($page['action']) && method_exists($controller, $page['action'])) {
					call_user_func_array(array($controller, $page['action']), $params);
				}
				else {
					call_user_func_array(array($controller, 'defaultAction'), $params);
				}
			}

			// Finished!
			exit();
		}
	}

	// If we got here then the page is not defined in pages.json
	header("HTTP/1.0 404 Not Found");
	echo "<h1>404 Not Found.</h1>";
	echo "The page that you have requested could not be found.";
	echo "<h2>Doh! Bad route.</h2>";
	exit();
}
else {
	die("Site Error: Missing " . ROUTES_FILE);
}