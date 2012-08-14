<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gfw;

use Symfony\Component\HttpFoundation\Request;
use Gfw\View;

class Container extends \Pimple
{
    public function __construct(Request $request)
    {
        $this['request'] = $request;

        $this['parser'] = function ($c) {
            return new Parser($c['request']);
        };
        $this['instancer'] = function ($c) {
            return new Instancer($c);
        };
        $this['responser'] = function ($c) {
            return new Responser($c['instancer']);
        };
    }

    public function setUpViewEnvironment($cachePath, $cacheAutoReload)
    {
        $this['view'] = $this->share(function() use ($cachePath, $cacheAutoReload) {
            return new View($cachePath, $cacheAutoReload);
        });
    }
}