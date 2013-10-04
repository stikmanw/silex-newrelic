<?php
/*
 * This file is part of the NewRelic Silex package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewRelic\Silex;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Intouch\Newrelic\Newrelic;

class NewRelicServiceProvider implements ServiceProviderInterface
{
    const DEFAULT_APPNAME = 'Silex PHP Application';
    const DEFAULT_TRANSACTION_NAME_METHOD = 'uri';
    const DEFAULT_LICENSE = null;
    const DEFAULT_TRANSACTION_TRACER_DETAIL = 1;
    const DEFAULT_LOG_LEVEL = 'info';
    const DEFAULT_FRAMEWORK = 'no_framework';
    const DEFAULT_CAPTURE_PARAMS = false;
    const DEFAULT_INGORED_PARAMS = '';
    const DEFAULT_AUTO_INSTRUMENT = 1;
    const DEFAULT_RECORD_SQL = 'off';
    const DEFAULT_SLOW_SQL = true;
    const DEFAULT_EXCEPTION_IF_NOT_INSTALLED = true;

    public function register(Application $app)
    {
        $app['newrelic'] = $app->share(function($app) {
            $this->setDefaultOptions($app);
            $this->applyOptions($app);
            $this->setupAfterMiddleware($app);

            return new Newrelic($app['newrelic.options']['exception_if_not_installed']);
        });

        $app['newrelic.setup_module'] = $app->share(function($app) {
            return new SetupModule(new IniConfigurator());
        });
    }

    protected function setDefaultOptions(Application $app)
    {
        if (!isset($app['newrelic.options'])) {
            $app['newrelic.options'] = array();
        }

        $app['newrelic.options'] = array_merge(
            array(
                'exception_if_not_installed' => self::DEFAULT_EXCEPTION_IF_NOT_INSTALLED,
                'transaction_name_method' => self::DEFAULT_TRANSACTION_NAME_METHOD,
                'application_name' => self::DEFAULT_APPNAME,
                'license' => self::DEFAULT_LICENSE,
                'transaction_tracer_detail' => self::DEFAULT_TRANSACTION_TRACER_DETAIL,
                'log_level' => self::DEFAULT_LOG_LEVEL,
                'framework' => self::DEFAULT_FRAMEWORK,
                'capture_params' => self::DEFAULT_CAPTURE_PARAMS,
                'ignored_params' => self::DEFAULT_INGORED_PARAMS,
                'auto_instrument' => self::DEFAULT_AUTO_INSTRUMENT,
                'record_sql' => self::DEFAULT_RECORD_SQL,
                'slow_sql' => self::DEFAULT_SLOW_SQL
            ), $app['newrelic.options']
        );
    }


    protected function applyOptions(Application $app)
    {
        $app['newrelic.setup_module']->applyOptions($app['newrelic.options']);
    }

    protected function setupAfterMiddleware(Application $app)
    {
        $app->after(function (Request $request, Response $response) use ($app) {
            switch ($app['newrelic.options']['transaction_name_method']) {
                case 'route':
                    $name = $request->attributes->get('_route');
                    break;
                case 'uri':
                default:
                    $name = $request->getRequestUri();
                    break;
            }
            
            $app['newrelic']->nameTransaction($name);
        });
    }

    public function boot(Application $app)
    {
    }
}