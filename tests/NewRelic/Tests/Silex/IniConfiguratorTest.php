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
use NewRelic\Silex\IniConfigurator;


class IniConfiguratorTest extends TestCase
{
    protected function createIniConfigurator()
    {
        return new IniConfigurator();
    }

    public function testSet()
    {
        $configurator = $this->createIniConfigurator();
        $configurator->set('memory_limit', '512M');

        $this->assertSame('512M', ini_get('memory_limit'));
    }

    public function testGet()
    {
        ini_set('memory_limit', '512M');

        $configurator = $this->createIniConfigurator();
        $this->assertSame('512M', $configurator->get('memory_limit'));
    }
}