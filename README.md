# php-gozer
## A simple PHP framework for web sites and web services.

Gozer is a set of custom base classes and commonly used composer packages consisting of the following:

- CoreAPI <- Base class for an API/webservice. includes an optional OAuth2 mechanism
- CoreController <- Base class for controllers. Initializes Doctrine and Twig plus helper function common to all controllers.

The following composer packages are required by Gozer and are installed with it:

- Doctrine ORM
- Twig
- OAuth2
- Altorouter
- KLogger (Jimgitsit fork)

### Installation
Composer:

	"repositories": [{
		"type": "package",
		"package": {
		"name": "jimgitsit/klogger",
		"version": "dev-master",
		"source": {
			"url": "https://github.com/Jimgitsit/KLogger.git",
			"type": "git",
			"reference": "master"
			}
		}
	}],
	"require": {
	  "jimgitsit/klogger": "dev-master"
	}

### Configuration
Copy gozer_config_template.php to <project_root>/app/config/gozer_config.php and edit as needed. Should be pretty self-explanatory.

### Usage


#### Routing


#### Controllers


#### Views
The Twig templating engine is included with this framework by default via composer and available in controllers (that extend CoreController) via `$this->twig`. Template files are stored in app/views by default.

#### API / Webservices
API or webservice controllers should extend the CoreAPI class. If you want to use OAuth2, execute create_oauth_tables.sql on your database to add the required tables.

#### Models
Doctrine ORM is used for database access. Entities are stored in app/models by default. See the Doctrine documentation for a list of supported databases and usage. Controllers extending CoreController can access the EntityManager via `$this->getEntityManager()`. Whenever you add a new model you will need to do a `#php composer.phar update` to update the autoloader files.

#### See Also
- Composer: https://getcomposer.org/doc/
- Doctrine: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/
- Twig: http://twig.sensiolabs.org/documentation
- AltoRouter: https://github.com/dannyvankooten/AltoRouter
