<?php
/*
 * This file is part of the Skeetr package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewRelic\Tests\Silex;

use Silex\Application;
use NewRelic\Silex\NewRelicServiceProvider;
use Mockery as m;

class NewRelicServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $provider = new NewRelicServiceProvider();

        $app = new Application();
        $app->register($provider);
        $app['newrelic.options'] = array('exception_if_not_installed' => false);

        $this->assertInstanceOf('NewRelic\Silex\IniConfigurator', $app['newrelic.ini_configurator']);
        $this->assertInstanceOf('NewRelic\Silex\SetupModule', $app['newrelic.setup_module']);

        $app['newrelic.setup_module'] = m::mock('NewRelic\Silex\SetupModule');
        $app['newrelic.setup_module']->shouldReceive('loadConfiguration');

        $this->assertInstanceOf('Intouch\Newrelic\Newrelic', $app['newrelic']);
    }
}
