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
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Gfw\Container;
use Gfw\Exception;

class Web
{
    private $request;
    private $container;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->container = new Container($this->request);
        $this->setUpAutoloader();
    }

    private function setUpAutoloader()
    {
        $this->loader = new UniversalClassLoader();
        $this->loader->register();
    }

    public function setUpViewEnvironment($cachePath, $cacheAutoReload)
    {
        $this->container->setUpViewEnvironment($cachePath, $cacheAutoReload);
    }

    public function getResponse()
    {
        return $this->container['responser']->getResponse();
    }

    public function registerNamespace($namespace, $paths)
    {
        $this->loader->registerNamespace($namespace, $paths);
        $this->container['view']->registerNamespace($namespace, $paths);
    }

    public function registerConfFromPath($path)
    {
        if (!is_file($path)) {
            throw new Exception('Configuration file not found');
        }
        $this->container->setUpConfiguration(require $path);
    }
}