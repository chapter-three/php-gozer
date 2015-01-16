<?php

/**
 * This is the bootstrap entry point for all requests.
 * Requires that all requests be routed to this file. (ie. in an .htaccess file).
 */

require_once('../../vendor/autoload.php');
require_once('../config/config.php');

// *** Parse the request URL and load the appropriate page ***
if( is_file( ROUTES_FILE ) ) {
	$pages = json_decode( file_get_contents( ROUTES_FILE ), true );
	if (!$pages) {
		echo "<h1>Error: Invalid routes file.</h1>";
		echo "The routes.json file could not be parsed.";
		exit();
	}
	
	$reqPage = $_SERVER[ 'REQUEST_URI' ];
	$query = '?' . $_SERVER[ 'QUERY_STRING' ];
	$reqPage = str_replace( $query, '', $reqPage );
	if( $reqPage != '/' && substr( $reqPage, -1 ) == '/' ) {
		$reqPage = substr( $reqPage, 0, strlen( $reqPage ) - 1 );
	}
	
	foreach( $pages as $page ) {
		if( $page[ 'url' ] == $reqPage ) {
			if( !empty( $page[ 'redirect' ] ) ) {
				header( 'Location:' . $page[ 'redirect' ] );
				exit();
			}
			
			$controller = null;
			if( !empty( $page[ 'controller' ] ) ) {
				if( !is_file( BASE_PATH . '/application/controllers/' . $page[ 'controller' ] . '.php' ) ) {
					die( "Controller '" . BASE_PATH . "/application/controllers/{$page[ 'controller' ]} for page '{$page->url}' could not be found." );
				}
				require_once( $page[ 'controller' ] . '.php' );
				$className = '';
				if( strstr( $page[ 'controller' ], '/' ) ) {
					//$className = str_replace( '.php', '', substr( $page[ 'controller' ], strrpos( $page[ 'controller' ], '/' ) + 1 ) );
					$className = substr( $page[ 'controller' ], strrpos( $page[ 'controller' ], '/' ) + 1 );
				}
				else {
					//$className = str_replace( '.php', '', $page[ 'controller' ] );
					$className = $page[ 'controller' ];
				}
				
				if( isset( $page[ 'view' ] ) ) {
					$controller = new $className( $page[ 'view' ] );
				}
				else {
					// The controller does not have a view (most likely an API controler)
					$controller = new $className();
				}
				
				if (isset($page['action']) && method_exists($controller, $page['action'])) {
					call_user_func(array($controller, $page['action']));
				}
				else {
					$controller->defaultAction();
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
	die( "Site Error: Missing " . ROUTES_FILE );
}