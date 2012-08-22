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
use Symfony\Component\HttpFoundation\Response;
use Gfw\View;
use Gfw\Instancer;
use Gfw\Parser;
use Gfw\Conf;
use Gfw\Db;
use Gfw\Response\Json;

class Container extends \Pimple
{
    public function __construct(Request $request)
    {
        $this['request'] = $request;

        $this['parser'] = function ($c) {
            return new Parser($c['request']);
        };
        $this['response'] = function () {
            return new Response();
        };
        $this['jsonResponse'] = function () {
            return new Json();
        };
        $this['instancer'] = function ($c) {
            return new Instancer($c);
        };
        $this['responser'] = function ($c) {
            return new Responser($c['instancer']);
        };

        $this['db'] = function ($c) {
            return new Db($c);
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
     * @return Gfw\Response\Json
     */
    public function getJsonResponse()
    {
        return $this['jsonResponse'];
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this['response'];
    }

    /**
     * @return Gfw\Db
     */
    public function getDb()
    {
        return $this['db'];
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