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
use Gfw\Instancer;
use Gfw\Parser;
use Gfw\Conf;

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

    public function setUpConfiguration($conf)
    {
        $this['conf'] = $this->share(function() use ($conf) {
            return new Conf($conf);
        });
    }

    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this['request'];
    }

    /**
     * @return Gfw\Conf
     */
    public function getConf()
    {
        return $this['conf'];
    }

    /**
     * @return Gfw\Instancer
     */
    public function getInstance()
    {
        return $this['instancer'];
    }

    /**
     * @return Gfw\Parser
     */
    public function getParser()
    {
        return $this['parser'];
    }

    /**
     * @return Gfw\View
     */
    public function getView()
    {
        return $this['view'];
    }

    /**
     * @return Gfw\View
     */
    public function getContainer()
    {
        return $this;
    }
}