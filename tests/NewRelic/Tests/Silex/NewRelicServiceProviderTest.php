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

class NewRelicServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $app = new Application();
        $app->register(new NewRelicServiceProvider());

        $this->assertInstanceOf('Mandango\Mondator\Mondator', $app['newrelic']);
    }
}
