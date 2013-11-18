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
    protected $installed;

    const DEFAULT_APPNAME = 'Silex PHP Application';
    const DEFAULT_TRANSACTION_NAME_METHOD = 'uri';
    const DEFAULT_DISABLE_AUTO_RUM = false;
    const DEFAULT_TRANSACTION_TRACER_DETAIL = 1;
    const DEFAULT_CAPTURE_PARAMS = false;
    const DEFAULT_INGORED_PARAMS = '';
    const DEFAULT_TRANSACTION_NAME = 'none';

    public function __construct( $throw = false )
    {
        $this->installed = extension_loaded('newrelic') && function_exists('newrelic_set_appname');
    }

    public function register(Application $app)
    {
        if (!$this->installed) {
            return;
        }

        $self = $this;
        $app['newrelic'] = $app->share(function($app) use ($self) {
            $self->setDefaultOptions($app);
            $self->applyOptions($app);

            return new Newrelic(@$app['newrelic.options']['exception_if_not_installed']);
        });

        $app['newrelic.ini_configurator'] = $app->share(function($app) {
            return new IniConfigurator();
        });

        $app['newrelic.setup_module'] = $app->share(function($app) {
            return new SetupModule($app['newrelic.ini_configurator']);
        });
        
        if (!isset($app['newrelic.options'])) {
            $app['newrelic.options'] = array();
        }
    }

    public function getDefaultOptions()
    {
        return array(
            'transaction_name_method' => self::DEFAULT_TRANSACTION_NAME_METHOD,
            'application_name' => self::DEFAULT_APPNAME,
            'transaction_tracer_detail' => self::DEFAULT_TRANSACTION_TRACER_DETAIL,
            'capture_params' => self::DEFAULT_CAPTURE_PARAMS,
            'ignored_params' => self::DEFAULT_INGORED_PARAMS,
            'disable_auto_rum' => self::DEFAULT_DISABLE_AUTO_RUM
        );
    }

    public function setDefaultOptions(Application $app)
    {
        $app['newrelic.options'] = array_merge(
            $this->getDefaultOptions(), 
            $app['newrelic.options']
        );

    }

    public function applyOptions(Application $app)
    {
        $app['newrelic.setup_module']->loadConfiguration($app['newrelic.options']);
    }

    protected function setupAfterMiddleware(Application $app)
    {
        $app->after(function (Request $request, Response $response) use ($app){
            switch ($app['newrelic.options']['transaction_name_method']) {
                case 'route':
                    $name = $request->attributes->get('_route');
                    break;
                case 'uri':
                default:
                    $name = $request->getRequestUri();
                    break;
            }

            if (!$name) {
                $name = self::DEFAULT_TRANSACTION_NAME;
            }

            $app['newrelic']->nameTransaction($name);
        });
    }

    protected function configureNewRelic(Application $app)
    {
        if (@$app['newrelic.options']['disable_auto_rum']) {
            $app['newrelic']->disableAutoRUM();
        }

        if ($app['newrelic.options']['application_name']) {
            $app['newrelic']->setAppName($app['newrelic.options']['application_name']);
        }
    }

    public function boot(Application $app)
    {
        if (!$this->installed) {
            return;
        }

        $this->configureNewRelic($app);
        $this->setupAfterMiddleware($app);
    }
}