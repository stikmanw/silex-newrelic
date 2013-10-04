<?php
/*
 * This file is part of the NewRelic Silex package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewRelic\Tests\Silex;
use NewRelic\Silex\SetupModule;
use Mockery as m;

class SetupModuleTest extends TestCase
{
    protected function createSetupModule()
    {
        $this->ini = m::mock('NewRelic\Silex\IniConfigurator');
        return new SetupModule($this->ini);
    }

    /**
     * @dataProvider provider
     */
    public function testLoadConfiguration($name, $varname)
    {
        $setup = $this->createSetupModule();
        $value = new \stdClass();

        $this->ini->shouldReceive('set')
            ->with($varname, $value)->once()->andReturn(null);

        $setup->loadConfiguration(array($name => $value));
    }

    public function provider()
    {
        return array(
            array('application_name', 'newrelic.appname'),
            array('license', 'newrelic.license'),
            array('transaction_tracer_detail', 'newrelic.transaction_tracer.detail'),
            array('log_level', 'newrelic.loglevel'),
            array('framework', 'newrelic.framework'),
            array('capture_params', 'newrelic.capture_params'),
            array('ignored_params', 'newrelic.ignored_params'),
            array('auto_instrument', 'newrelic.browser_monitoring.auto_instrument'),
            array('record_sql', 'newrelic.transaction_tracer.record_sql'),
            array('slow_sql', 'newrelic.transaction_tracer.slow_sql')
        );
    }
}