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

use UnexpectedValueException;

class SetupModule
{
    private $optionsMapping = array(
        'application_name' => 'newrelic.appname',
        'license' => 'newrelic.license',
        'transaction_tracer_detail' => 'newrelic.transaction_tracer.detail',
        'log_level' => 'newrelic.loglevel',
        'framework' => 'newrelic.framework',
        'capture_params' => 'newrelic.capture_params',
        'ignored_params' => 'newrelic.ignored_params',
        'auto_instrument' => 'newrelic.browser_monitoring.auto_instrument',
        'record_sql' => 'newrelic.transaction_tracer.record_sql',
        'slow_sql' => 'newrelic.transaction_tracer.slow_sql'
    );

    private $configurator;

    public function __construct(IniConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function loadConfiguration(Array $options)
    {
        foreach ($options as $name => $value) {
            if (isset($this->optionsMapping[$name])) {
                $name = $this->optionsMapping[$name];
                $this->configurator->set($name, $value);
            }
        }
    }

    protected function applyOption($name, $value)
    {
        if (!isset($this->optionsMapping[$name])) {
            return;
        }

        $varname = $this->optionsMapping[$name];
        $this->configurator->set($varname, $value);
    }
}