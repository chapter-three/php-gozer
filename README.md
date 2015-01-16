# php-gozer
## A simple PHP framework for web sites and web services.

This is designed to be a very simple framework to quickly get a web site or service up and running with as little complexity and overhead as possible while still containing common and often used functionality. By default it includes Composer, Doctrine ORM and Twig plus a custom router. The framework can be easily extended for specific needs using Composer.

### Instalation
1. Download zip and extract to your document root.
2. Configure a web server alias to point to the application/public directory.
3. From a terminal cd to application/public and run `#php composer.phar update`

### Configuration
Edit application/config/config.php as needed. Should be pretty self-explanatory.

### Usage
There are several example files included with the framework. Looking over those is probably the best way to learn how it works.

#### Routing
Routes are defined in application/config/routes.json using the following parameters:
- *url* (required)
  - A single slash (/) is the default for hostname.com/. Use any number of parameters in the url with %1/%2 etc. Note that for now parameters must be the last components of the URL (/admin/%1/users wont work).
- *controller* (required)
  - The class name for the controller. The file containing the class must be named the same as the class.
- *action* (optional)
  - The method in the controller to call. If not provided `defaultAction` is called.
- *view* (optional)
  - The view (Twig template) to use if one is needed.

#### Controllers
Controllers classes are kept in application/controllers and must extend the CoreController class. The file containing the controller class must be named the same as the class in order for the routing mechanism to find it. If a view is associated with the controller, Twig will be available through `$this->twig`. The Doctrine EntityManager is available through `$this->getEntityManager()`. Note: Doctrine is lazy loaded so is not loaded or initialized in the controller until `$this->getEntityManager()` is called. Whenever you add a new controller you will need to do a `#php composer.phar update` to update the autoloader files.

#### Views
Views are html files in application/views. The Twig templating engine is included with this framework by default via composer and available in controllers (that extend CoreController) via `$this->twig`.

#### API / Webservices
Api or webservice controllers are also kept in application/controllers and must extend the CoreAPI class. The file containing the api controller class must be named the same as the class in order for the routing mechanism to find it.

#### Models
Doctrine ORM is used for database access. Entities are stored in application/models by default. See the Doctrine documentation for a list of supported databases and usage. Controllers extending CoreController can access the EntityManager via `$this->getEntityManager()`. Whenever you add a new model you will need to do a `#php composer.phar update` to update the autoloader files.

#### See Also
- Composer: https://getcomposer.org/doc/
- Doctrine: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/
- Twig: http://twig.sensiolabs.org/documentation
