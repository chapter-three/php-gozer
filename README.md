# php-gozer
## A simple PHP framework for web sites and web services.

By default it includes Doctrine ORM and Twig plus a custom router.

### Instalation
1. Download zip and extract to your document root.
2. run `#php composer.phar install` or `#php composer.phar update`

### Configuration
Edit application/config/config.php as needed. Should be pretty self-explanitory.

### Usage

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

#### See Also
- Composer: https://getcomposer.org/doc/
- Doctrine: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/
- Twig: http://twig.sensiolabs.org/documentation
