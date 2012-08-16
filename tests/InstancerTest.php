<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gfw\Parser;
use Gfw\Instancer;
use Gfw\Exception;
use Gfw\Container;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

include_once __DIR__ . '/fixtures/namespace.php';

class InstancerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstace()
    {
        $request = Request::create('/foo/dymmy.json', 'GET', array());
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'json', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertTrue($instancer->getInstance() instanceof App\Foo\Dummy);
    }

    public function testCallActionWithoutParameters()
    {
        $request = Request::create('/foo/dummy.json', 'GET', array());
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'json', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertEquals(array('Hi', 'Gonzalo'), $instancer->invokeAction());
    }

    public function testCallActionWithParameters()
    {
        $request = Request::create('/foo/dummy.txt', 'GET', array());
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'txt', $request);
        $parser->expects($this->any())->method('getParameter')->with($this->equalTo('name'))->will(
            $this->returnValue('Gonzalo')
        );
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertEquals('Hi Gonzalo', $instancer->invokeAction());
    }

    public function testCallActionDIRequest()
    {
        $request = Request::create('/foo/dummy.html', 'GET', array('name' => 'Gonzalo'));
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'html', $request);
        $parser->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $instancer = new Instancer($this->getContainerFromParser($parser, '/foo/dummy.html', array('name' => 'Gonzalo')));
        $this->assertEquals('Hi Gonzalo', $instancer->invokeAction());
    }

    public function testMethodNotAllowed()
    {
        $request = Request::create('/annotations/dummy.json', 'GET', array());
        $parser    = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'json', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));
        try {
            $instancer->invokeAction();
            $this->assertTrue(FALSE, 'Un recheable code');
        } catch (Exception $e) {
            $this->assertEquals(405, $e->getCode());
            $this->assertEquals('Method GET not allowed', $e->getMessage());
        }
    }

    public function testReturnResponseObject()
    {
        $request = Request::create('/foo/dummy.action', 'GET', array());
        $parser    = $this->getParserForOneAction('App\\Foo', 'Dummy', 'action', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $out       = $instancer->invokeAction();
        $this->assertTrue($out instanceof Response);
    }

    public function testPostfix()
    {
        $request = Request::create('/annotations/dummy.action', 'GET', array());
        $parser           = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'action', $request);
        App\Logger::$flag = 0;
        $instancer        = new Instancer($this->getContainerFromParser($parser));
        $out              = $instancer->invokeAction();
        $this->assertEquals(1, App\Logger::$flag);
    }

    public function testwithMultiplePostfix()
    {
        $request = Request::create('/annotations/dummy.action2', 'GET', array());
        $parser           = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'action2', $request);
        App\Logger::$flag = 0;
        $instancer        = new Instancer($this->getContainerFromParser($parser));
        $out              = $instancer->invokeAction();
        $this->assertEquals(3, App\Logger::$flag);
    }

    public function testPrefix()
    {
        $request = Request::create('/annotations/dummy.withAuth', 'GET', array());
        $parser    = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'withAuth', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));
        try {
            $instancer->invokeAction();
            $this->assertTrue(FALSE, 'Un recheable code');
        } catch (Exception $e) {

            $this->assertEquals(401, $e->getCode());
            $this->assertEquals('Unauthorized', $e->getMessage());
        }
    }

    public function testRequestDIWithinConstructor()
    {
        $request = Request::create('/foo/dummy2.html', 'GET', array('name' => 'Gonzalo'));
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy2', 'html', $request);
        $parser->expects($this->any())->method('getRequest')->will(
            $this->returnValue($request)
        );
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertEquals('Hi Gonzalo', $instancer->invokeAction());
    }

    public function testHandling404()
    {
        $request = Request::create('/namespace/nonExixtentClass.nonExistentMethod', 'GET', array());
        $parser    = $this->getParserForOneAction('NonExixtent\\Namespace', 'NonExixtentClass', 'nonExistentMethod', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));

        try {
            $instancer->invokeAction();
            $this->assertTrue(FALSE, 'Un recheable code');
        } catch (Exception $e) {

            $this->assertEquals(404, $e->getCode());
            $this->assertEquals('Not Found', $e->getMessage());
        }
    }

    public function testHandling404_1()
    {
        $request = Request::create('/foo/nonExixtentClass.nonExistentMethod', 'GET', array());
        $parser    = $this->getParserForOneAction('App\\Foo', 'NonExixtentClass', 'nonExistentMethod', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));

        try {
            $instancer->invokeAction();
            $this->assertTrue(FALSE, 'Un recheable code');
        } catch (Exception $e) {

            $this->assertEquals(404, $e->getCode());
            $this->assertEquals('Not Found', $e->getMessage());
        }
    }

    public function testHandling404_2()
    {
        $request = Request::create('/foo/dummy2.nonExistentMethod', 'GET', array());
        $parser    = $this->getParserForOneAction('App\\Foo', 'Dummy2', 'nonExistentMethod', $request);
        $instancer = new Instancer($this->getContainerFromParser($parser));

        try {
            $instancer->invokeAction();
            $this->assertTrue(FALSE, 'Un recheable code');
        } catch (Exception $e) {

            $this->assertEquals(404, $e->getCode());
            $this->assertEquals('Not Found', $e->getMessage());
        }
    }

    private function getContainerFromParser(Parser $parser)
    {
        $container = new Container($parser->getRequest());
        $container['parser'] = function () use ($parser) {
            return $parser;
        };
        return $container;
    }

    /**
     * @param $namespace
     * @param $class
     * @param $action
     * @param $request
     * @return PHPUnit_Framework_MockObject_MockObject | Parser
     */
    private function getParserForOneAction($namespace, $class, $action, Request $request)
    {
        $fullClassName = "{$namespace}\\{$class}";
        $parser        = $this->getMockBuilder('Gfw\Parser')
                ->disableOriginalConstructor()
                ->getMock();
        $parser->expects($this->any())->method('getRequestMethod')->will($this->returnValue($request->getMethod()));
        $parser->expects($this->any())->method('getNamespace')->will($this->returnValue($namespace));
        $parser->expects($this->any())->method('getClassName')->will($this->returnValue($class));
        $parser->expects($this->any())->method('getAction')->will($this->returnValue($action));
        $parser->expects($this->any())->method('getClassFullName')->will($this->returnValue($fullClassName));
        $parser->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        return $parser;
    }
}