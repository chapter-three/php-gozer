<?php

// Required for doctrine command line tools.

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once('vendor/autoload.php');
require_once('application/config/config.php');

// replace with mechanism to retrieve EntityManager in your app
$paths = array('application/models');
$isDevMode = false;

// the connection configuration
$dbParams = array(
	'driver'   => 'pdo_mysql',
	'user'     => 'root',
	'password' => 'mysqlr00t',
	'dbname'   => 'doctrine',
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

return ConsoleRunner::createHelperSet($entityManager);