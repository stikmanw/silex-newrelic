NewRelic Silex Provider [![Build Status](https://travis-ci.org/mcuadros/silex-newrelic.png?branch=master)](https://travis-ci.org/mcuadros/silex-newrelic)
==============================

Integrate the [NewRelic PHP Agent API](https://newrelic.com/docs/php/the-php-api) into Silex framework

Requirements
------------

* PHP 5.3.x
* intouch/newrelic
* newrelic >= 3.1

Installation
------------

The recommended way to install NewRelic/Silex is [through composer](http://getcomposer.org).
You can see [the package information on Packagist.](https://packagist.org/packages/mcuadros/silex-newrelic)

```JSON
{
    "require": {
        "mcuadros/silex-newrelic": "dev-master"
    }
}
```

Parameters
------------

Set all parameters in the array $app['newrelic.options']:

* ```application_name``` (default 'Silex PHP Application'): Sets the name of the application to name.
* ```transaction_name_method``` (default 'uri'): if 'uri' the request URI will be used as transaction name, if 'route' will be used the alias name from the route.
* ```transaction_tracer_detail``` (default 1): check it at [newrelic.transaction_tracer.detail](http://docs.newrelic.com/docs/php/php-agent-phpini-settings)
* ```capture_params``` (default false): check it at [newrelic.capture_params](http://docs.newrelic.com/docs/php/php-agent-phpini-settings)
* ```ignored_params``` (default ''): check it at [newrelic.ignored_params](http://docs.newrelic.com/docs/php/php-agent-phpini-settings)
* ```disable_auto_rum``` (default false): Prevents the output filter from attempting to insert RUM JavaScript for this current transaction. Useful for AJAX calls, for example.


Registrating
------------

```PHP
$app->register(new NewRelic\Silex\NewRelicServiceProvider());
$app['newrelic.options'] = array(
    'application_name' => 'Example PHP Application',
    'transaction_name_method' => 'route'
);
```

Tests
-----

Tests are in the `tests` folder.
To run them, you need PHPUnit.
Example:

    $ phpunit --configuration phpunit.xml.dist


License
-------

MIT, see [LICENSE](LICENSE)