<?php

// *** Set up include paths *** TODO: eventually replace this with autoload
$paths = array(
	BASE_PATH . '/application/config',
	BASE_PATH . '/application/core',
	BASE_PATH . '/application/controllers',
	BASE_PATH . '/application/models',
	BASE_PATH . '/vendor',
	BASE_PATH . '/vendor/twig/twig/lib/Twig/Loader'
);

$addPath = '';
foreach ($paths as $path) {
	$addPath .= $path . PATH_SEPARATOR;
}
$set = set_include_path(get_include_path() . PATH_SEPARATOR . $addPath);