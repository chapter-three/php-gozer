<?php

// 'dev' or 'prod'
define('ENV', 'dev'); // TODO: This is server and install specific and so should be elsewhere.

// This shouldn't need to be changed
define('BASE_PATH', dirname(dirname(dirname(__FILE__))));

/*** Doctrine ***/
define('DOCTRINE_ENTITIES_DIR', 'app/models');
define('DOCTRINE_DB_DRIVER', 'pdo_mysql');
define('DOCTRINE_DB_USER', '');
define('DOCTRINE_DB_PASSWORD', '');
define('DOCTRINE_DB_NAME', '');
define('DOCTRINE_DB_HOST', '');

/*** OAuth2 ***/
define('API_USE_OAUTH', true);
define('OAUTH_STORAGE_TYPE', 'pdo');
// Default is 1 hour (3600 seconds)
define('OAUTH_TOKEN_LIFETIME', 3600);
// Only supports 'client_credentials' and 'password' for now.
define('OAUTH_GRANT_TYPES', serialize(array('client_credentials')));
define('OAUTH_ISSUE_REFRESH_TOKENS', true);
// Default is 14 days (2419200 seconds).
define('OAUTH_REFRESH_TOKEN_LIFETIME', 2419200);

/*** Twig ***/
define('TWIG_TEMPLATE_DIR', BASE_PATH . '/app/views');
define('TWIG_CACHE_DIR', BASE_PATH . '/cache/twig');