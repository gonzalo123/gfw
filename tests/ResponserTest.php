<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gfw\Responser;
use Gfw\Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponserTest extends \PHPUnit_Framework_TestCase
{
    private $instancer;

    public function testSimpleStringResponse()
    {
        $this->instancer->expects($this->any())->method('invokeAction')->will($this->returnValue('Hi'));

        $responser = new Responser($this->instancer);
        $response  = $responser->getResponse();
        $this->assertTrue($response instanceof Response);
        $this->assertEquals('Hi', $response->getContent());
    }

    public function setUp()
    {
        $this->instancer = $this->getMockBuilder('Gfw\Instancer')
                ->disableOriginalConstructor()
                ->getMock();
    }

    public function testMethodNotAllowed()
    {
        $this->instancer->expects($this->any())->method('invokeAction')->will(
            $this->throwException(new Exception("Method GET not allowed", 405))
        );

        $responser = new Responser($this->instancer);
        $response  = $responser->getResponse();
        $this->assertTrue($response instanceof Response);
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testInstancerReturnsResponseObject()
    {
        $this->instancer->expects($this->any())->method('invokeAction')->will($this->returnValue(new Response('Hi')));

        $responser = new Responser($this->instancer);
        $response  = $responser->getResponse();
        $this->assertTrue($response instanceof Response);
        $this->assertEquals('Hi', $response->getContent());
    }
}