NewRelic Silex Provider [![Build Status](https://travis-ci.org/stikmanw/silex-newrelic.svg?branch=master)](https://travis-ci.org/stikmanw/silex-newrelic)
==============================
Integrate the [NewRelic PHP Agent API](https://newrelic.com/docs/php/the-php-api) into Silex framework. This was originally created by [mcuadros](https://github.com/mcuadros).  Credit goes to him for his hardwork. 

Requirements
------------

* PHP 5.3.x
* [intouch/newrelic](https://github.com/In-Touch/newrelic)
* newrelic >= 3.1

Versions
--------
*This project recently changed hands. See the below updated verions for information on how to require correct dependency.*

* v0.1.0 - Original Repo state used ini settings supported by older versions of NewRelic PHP Agent
* v1.0.0 - Removed ini settings and replaced with more direct hooks into Intouch/NewRelic library

Installation
------------

The recommended way to install NewRelic/Silex is [through composer](http://getcomposer.org).
You can see [the package information on Packagist.](https://packagist.org/packages/mcuadros/silex-newrelic)

```JSON
{
    "require": {
        "stikmanw/silex-newrelic": "^v1.0"
    }
}
```

Methods
-------
* ```newrelic.custom_parameter``` ( string $key, $value ): Assign a custom parameter to be captured for the request by NewRelic Agent. Details: [NewRelic custom_parameter](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-custom-param)
* ```newrelic.custom_metric``` (string $name, mixed $value): Assign a metric name and value to be captured by NewRelic Agent. 
Details: [NewRelic custom_metric](https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-custom-metric)
Parameters
------------

Set all parameters in the array $app['newrelic.options']:

* ```application_name``` (default 'Silex PHP Application'): Sets the name of the application to name.
* ```transaction_name_method``` (default 'uri'): if 'uri' the request URI will be used as transaction name, if 'route' will be used the alias name from the route.
* ```transaction_tracer_detail``` (default 1): check it at  [newrelic.transaction_tracer.detail](http://docs.newrelic.com/docs/php/php-agent-phpini-settings) **not supported 1.0.0**
* ```capture_params``` (default false): determine if the request should capture parameters specified by NewRelic docs. 
[newrelic.capture_params](http://docs.newrelic.com/docs/php/php-agent-phpini-settings)
* ```ignored_params``` (default ''): check it at  [newrelic.ignored_params](http://docs.newrelic.com/docs/php/php-agent-phpini-settings)  **not supported 1.0.0**
* ```disable_auto_rum``` (default false): Prevents the output filter from attempting to insert RUM JavaScript for this current transaction. Useful for AJAX calls, for example.
* ```ignored_transaction``` (default false): do not send the transaction for tracking to the agent.  **added 1.0.0** [newrelic.ignored_params](http://docs.newrelic.com/docs/php/php-agent-phpini-settings)
* ```custom_params``` (default array()):list of custom params to assign to the request, see custom_params method above. **added 1.0.0**
* ```custom_metrics``` (default array()): custom metrics to setup when the provider is registered, see custom_metrics method above. **added 1.0.0**

Registering
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
