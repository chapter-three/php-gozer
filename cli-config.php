<?php

// Required for doctrine command line tools.

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once('vendor/autoload.php');

$paths = array(
	DOCTRINE_ENTITIES_DIR, 
	BASE_PATH . '/system/src/models'
);

$isDevMode = false;

// the connection configuration
$dbParams = array(
	'driver'   => DOCTRINE_DB_DRIVER,
	'user'     => DOCTRINE_DB_USER,
	'password' => DOCTRINE_DB_PASSWORD,
	'dbname'   => DOCTRINE_DB_NAME,
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

return ConsoleRunner::createHelperSet($entityManager);