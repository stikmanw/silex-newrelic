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
    const DEFAULT_INGORE_TRANSACTION = null;
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

            return new Newrelic();
        });
        
        if (!isset($app['newrelic.options'])) {
            $app['newrelic.options'] = array();
        }

        /**
         * shortcut method for adding custom metrics to newrelic instance
         * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-custom-metric
         */
        $app['newrelic.custom_metric'] = $app->share(function($name, $value) use ($app) {
            $app['newrelic']->addCustomMetric($name, $value);
        });

        /**
         * shortcut method to add new parameters to newrelic instance
         * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-custom-param
         */
        $app['newrelic.custom_parameter'] = $app->share(function($key, $value) use ($app) {
            $app['newrelic']->addCustomParameter($key, $value);
        });
    }

    public function getDefaultOptions()
    {
        return array(
            'transaction_name_method' => self::DEFAULT_TRANSACTION_NAME_METHOD,
            'application_name' => self::DEFAULT_APPNAME,
            'capture_params' => self::DEFAULT_CAPTURE_PARAMS,
            'ignored_transaction' => self::DEFAULT_INGORE_TRANSACTION,
            'disable_auto_rum' => self::DEFAULT_DISABLE_AUTO_RUM,
            'custom_metrics' => null,
            'custom_params' => null,

            // these options do nothing, but are left for backwards support
            'transaction_tracer_detail' => self::DEFAULT_TRANSACTION_TRACER_DETAIL,
            'ignore_params' => '',
        );
    }

    public function setDefaultOptions(Application $app)
    {
        $app['newrelic.options'] = array_merge(
            $this->getDefaultOptions(), 
            $app['newrelic.options']
        );

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
                $name = NewRelicServiceProvider::DEFAULT_TRANSACTION_NAME;
            }

            $app['newrelic']->nameTransaction($name);
        });
        
        $app->error(function (\Exception $e) use ($app) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                if ($e->getStatusCode() >= 400 && $e->getStatusCode() < 500) {
                    // Ignore client-side errors
                    return;
                }
            }

            $app['newrelic']->noticeError($e->getMessage(), $e);
        });
    }

    /**
     * For a complete overview of the newrelic configuration options see link
     * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api
     * @param Application $app
     */
    protected function configureNewRelic(Application $app)
    {
        if (
            isset($app['newrelic.options']['application_name']) &&
            $app['newrelic.options']['application_name']
        ) {
            $app['newrelic']->setAppName($app['newrelic.options']['application_name']);
        }

        /**
         * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-rum-disable
         */
        if (
            isset($app['newrelic.options']['disable_auto_rum']) &&
            $app['newrelic.options']['disable_auto_rum']
        ) {
            $app['newrelic']->disableAutoRUM();
        }

        /**
         * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-capture-params
         */
        if (
            isset($app['newrelic.options']['capture_params']) &&
            $app['newrelic.options']['capture_params']
        ) {
            $app['newrelic']->captureParams($app['newrelic.options']['capture_params']);
        }

        /**
         * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-ignore-transaction
         */
        if (
            isset($app['newrelic.options']['ignore_transactions']) &&
            $app['newrelic.options']['ignore_transaction']
        ) {
            $app['newrelic']->ignoreTransaction();
        }

        if (
            isset($app['newrelic.options']['custom_metrics']) &&
            is_array($app['newrelic.options']['custom_metrics'])
        ) {

            foreach($app['newrelic.options']['custom_metrics'] as $name => $value) {
                $app['newrelic.custom_metric']($name, $value);
            }

        }

        if (
            isset($app['newrelic.options']['custom_params']) &&
            is_array($app['newrelic.options']['custom_params'])
        ) {

            foreach($app['newrelic.options']['custom_params'] as $name => $value) {
                $app['newrelic.custom_params']($name, $value);
            }

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
