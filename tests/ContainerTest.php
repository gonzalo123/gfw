<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gfw\Container;
use Gfw\Responser;
use Gfw\Parser;
use Gfw\View;
use Gfw\Instancer;

use Symfony\Component\HttpFoundation\Request;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testContainer()
    {
        $request = Request::create('/foo/var.json', 'GET');
        $container = new Container($request);
        $this->assertTrue($container['request'] instanceof Request);
    }

    public function testContainer2()
    {
        $request = Request::create('/foo/var.json', 'GET');
        $container = new Container($request);
        $this->assertTrue($container['responser'] instanceof Responser);
        $this->assertTrue($container['instancer'] instanceof Instancer);
        $this->assertTrue($container['parser'] instanceof Parser);
    }

    public function testViewNotInitialized()
    {
        $request = Request::create('/foo/var.json', 'GET');
        $container = new Container($request);
        try {
            $view = $container['view'];
        } catch (\Exception $e) {
            $this->assertEquals('Identifier "view" is not defined.', $e->getMessage());
        }
    }

    public function testView()
    {
        $request = Request::create('/foo/var.json', 'GET');
        $container = new Container($request);
        $container->setUpViewEnvironment(__DIR__, TRUE);
        $this->assertTrue($container['view'] instanceof View);
    }

    public function testContainerFactoryMethods()
    {
        $request = Request::create('/foo/var.json', 'GET');
        $container = new Container($request);
        $container['view'] = $container->share(function() {
            return new View(__DIR__ . "/cache" . '/templates', TRUE);
        });

        $container['view']->registerNamespace('App', __DIR__ . '/templates');
        $this->assertTrue($container->getRequest() instanceof Request);
        $this->assertTrue($container->getInstance() instanceof Instancer);
        $this->assertTrue($container->getParse() instanceof Parser);
        $this->assertTrue($container->getView() instanceof View);
        $this->assertTrue($container->getContainer() instanceof Container);
    }
}