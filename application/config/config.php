<?php

// Gozer
define('BASE_PATH', str_replace('/application/public', '', $_SERVER['DOCUMENT_ROOT']));
define('ROUTES_FILE', BASE_PATH . '/application/config/routes.json');
define('ENV', 'dev'); // TODO: This is server and install specific and so should be elsewhere.

// Twig
define('TWIG_TEMPLATE_DIR', BASE_PATH . '/application/views');
define('TWIG_CACHE_DIR', BASE_PATH . '/system/cache/twig');

// Doctrine
define('DOCTRINE_ENTITIES_DIR', BASE_PATH . '/application/models');
define('DOCTRINE_DB_DRIVER', 'pdo_mysql');
define('DOCTRINE_DB_NAME', 'doctrine');
define('DOCTRINE_DB_USER', 'root');
define('DOCTRINE_DB_PASSWORD', 'mysqlr00t');