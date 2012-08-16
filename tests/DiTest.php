<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Gfw\Instancer;
use Gfw\Parser;
use Gfw\View;
use Gfw\Container;

include_once __DIR__ . '/fixtures/namespaceDI.php';

class DiTest extends \PHPUnit_Framework_TestCase
{
    public function testViewDIOverAnnotation()
    {
        $request = Request::create('/index.html', 'GET');
        $container = new Container($request);
        $container->setUpViewEnvironment(__DIR__ . "/cache" . '/templates', TRUE);
        $container->getView()->registerNamespace('App', __DIR__ . '/templates');

        $instancer = new Instancer($container);
        $this->assertEquals('Hi Gonzalo', $instancer->invokeAction());
    }
}