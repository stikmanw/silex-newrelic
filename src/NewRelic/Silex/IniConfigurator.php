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

class IniConfigurator
{
    public function set($varname, $value)
    {
        ini_set($varname, $value);

        if (!$this->match($varname, $value)) {
            throw new UnexpectedValueException(sprintf(
                'Unable to set value "%s" in %s',
                $value, $varname
            ));
        }
    }

    public function match($varname, $value)
    {
        return $this->get($varname) == $value;
    }

    public function get($varname)
    {
        return ini_get($varname);
    }

    public function restore($varname)
    {
        return ini_restore($varname);
    }
}