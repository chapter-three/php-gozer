# php-gozer
## A simple PHP sudo-framework for web sites and web services.

Gozer is a set of custom base classes and commonly used composer packages. The core base classes are as follows:

- CoreAPI <- Base class for an API/webservice. includes an optional OAuth2 mechanism
- CoreController <- Base class for controllers. Initializes Doctrine and Twig plus helper function common to all controllers.
- CoreAPIResponse.php <- An interface for API responders.
- CoreAPIResponseDefault.php <- The default API response which simply returns the response object as is.
- CoreAPIResponseJSON.php <- A responder for JSON.

The following composer packages are required by Gozer and are installed with it:

- Doctrine ORM
- Twig
- OAuth2 Server
- Altorouter
- KLogger (Jimgitsit fork)
- Sympohony YAML

### Installation
From the Command Line:

	composer require jimgitsit/php-gozer:dev-master

In your `composer.json`:

	"require": {
	  "jimgitsit/php-gozer": "2.0.*@dev",
	}

### Configuration
Copy gozer_config_template.php to <project_root>/app/config/site_config.php and edit as needed. Should be pretty self-explanatory.

### Usage


#### Routing
Routing is accomplised with Altorouter and Symphony YAML. Create a routes file <project_root>/app/config/routes.yaml then copy the bootstrap file sample_index.php to your public directory, typeically <project_root>/app/public. A sample route would look something like this:
```
default:
    method: "GET|POST"
    route: "/api"
    controller: "MyService"
    action: "defaultAction"
```
Where `defaultAction` is a method of the `MyService` class. Refer to the [Altorouter](http://altorouter.com/) documentaiton for further information.

#### Controllers
Generic or UI controllers should inherit from the CoreController class. In the case of a UI controller call the `initTwig()` method to initialize the templating engine.

#### Views
The Twig templating engine is included with this framework by default via composer and available in controllers (that extend CoreController) via `$this->twig`. Template files are stored in <project_root>/app/views by default.

#### API / Webservices
API or webservice controllers should extend the CoreAPI class. If you want to use OAuth2, execute create_oauth_tables.sql on your database to add the required tables. By inheriting from CoreAPI you automically get the method `getOAuth2Token()`. Simply create a route to this method and you have an OAuth2 mechanizm ready to use.

#### Models
Doctrine ORM is used for database access. Entities are stored in app/models by default. See the Doctrine documentation for a list of supported databases and usage. Controllers extending CoreController can access the EntityManager via `$this->getEntityManager()`. Note, whenever you add a new model you will need to do a `#php composer.phar update` to update the autoloader files.

#### See Also
- [Composer](https://getcomposer.org/doc/)
- [Doctrine](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/)
- [Twig](http://twig.sensiolabs.org/documentation)
- [AltoRouter](https://github.com/dannyvankooten/AltoRouter)
