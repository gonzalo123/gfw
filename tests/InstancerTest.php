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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

include_once __DIR__ . '/fixtures/namespace.php';

class InstancerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstace()
    {
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'json');
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertTrue($instancer->getInstance() instanceof App\Foo\Dummy);
    }

    public function testCallActionWithoutParameters()
    {
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'json');
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertEquals(array('Hi', 'Gonzalo'), $instancer->invokeAction());
    }

    public function testCallActionWithParameters()
    {
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'txt');
        $parser->expects($this->any())->method('getParameter')->with($this->equalTo('name'))->will(
            $this->returnValue('Gonzalo')
        );
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertEquals('Hi Gonzalo', $instancer->invokeAction());
    }

    public function testCallActionDIRequest()
    {
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy', 'html');
        $parser->expects($this->any())->method('getRequest')->will(
            $this->returnValue(Request::create('/foo/dummy.html', 'GET', array('name' => 'Gonzalo')))
        );
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertEquals('Hi Gonzalo', $instancer->invokeAction());
    }

    public function testMethodNotAllowed()
    {
        $parser    = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'json', 'GET');
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
        $parser    = $this->getParserForOneAction('App\\Foo', 'Dummy', 'action', 'GET');
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $out       = $instancer->invokeAction();
        $this->assertTrue($out instanceof Response);
    }

    public function testPostfix()
    {
        $parser           = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'action', 'GET');
        App\Logger::$flag = 0;
        $instancer        = new Instancer($this->getContainerFromParser($parser));
        $out              = $instancer->invokeAction();
        $this->assertEquals(1, App\Logger::$flag);
    }

    public function testwithMultiplePostfix()
    {
        $parser           = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'action2', 'GET');
        App\Logger::$flag = 0;
        $instancer        = new Instancer($this->getContainerFromParser($parser));
        $out              = $instancer->invokeAction();
        $this->assertEquals(3, App\Logger::$flag);
    }

    public function testPrefix()
    {
        $parser    = $this->getParserForOneAction('App\\Annotations', 'Dummy', 'withAuth', 'GET');
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
        $parser = $this->getParserForOneAction('App\\Foo', 'Dummy2', 'html', 'GET');
        $parser->expects($this->any())->method('getRequest')->will(
            $this->returnValue(Request::create('/foo/dummy2.html', 'GET', array('name' => 'Gonzalo')))
        );
        $instancer = new Instancer($this->getContainerFromParser($parser));
        $this->assertEquals('Hi Gonzalo', $instancer->invokeAction());
    }

    public function testHandling404()
    {
        $parser    = $this->getParserForOneAction('NonExixtent\\Namespace', 'NonExixtentClass', 'nonExistentMethod');
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
        $parser    = $this->getParserForOneAction('App\\Foo', 'NonExixtentClass', 'nonExistentMethod');
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
        $parser    = $this->getParserForOneAction('App\\Foo', 'Dummy2', 'nonExistentMethod');
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
        $container = new \Pimple();
        $container['parser'] = function () use ($parser) {
            return $parser;
        };
        return $container;
    }

    /**
     * @param $namespace
     * @param $class
     * @param $action
     * @param $method
     * @return PHPUnit_Framework_MockObject_MockObject | Parser
     */
    private function getParserForOneAction($namespace, $class, $action, $method = 'GET')
    {
        $fullClassName = "{$namespace}\\{$class}";
        $parser        = $this->getMockBuilder('Gfw\Parser')
                ->disableOriginalConstructor()
                ->getMock();
        $parser->expects($this->any())->method('getRequestMethod')->will($this->returnValue($method));
        $parser->expects($this->any())->method('getNamespace')->will($this->returnValue($namespace));
        $parser->expects($this->any())->method('getClassName')->will($this->returnValue($class));
        $parser->expects($this->any())->method('getAction')->will($this->returnValue($action));
        $parser->expects($this->any())->method('getClassFullName')->will($this->returnValue($fullClassName));
        return $parser;
    }
}