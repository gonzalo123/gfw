<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gfw\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

include_once __DIR__ . '/fixtures/namespace.php';

class WebTest extends \PHPUnit_Framework_TestCase
{
    public function testIntegration()
    {
        $request  = Request::create('foo/dummy.txt', 'GET', array('name' => 'Gonzalo',));
        $gfw      = new Web($request);
        $response = $gfw->getResponse();
        $this->assertTrue($response instanceof Response);
        $this->assertEquals("Hi Gonzalo", $response->getContent());
    }

    /**
     * @expectedException  \Gfw\Exception
     */
    public function testLoadNonExistentConf()
    {
        $request = Request::create('foo/dummy.txt', 'GET', array('name' => 'Gonzalo',));
        $gfw     = new Web($request);
        $gfw->registerConfFromPath(__DIR__ . "nonExistentConf.php");
    }

    public function testLoadConf()
    {
        $request = Request::create('foo/dummy.txt', 'GET', array('name' => 'Gonzalo',));
        $gfw     = new Web($request);
        $gfw->registerConfFromPath(__DIR__ . "/fixtures/Conf.php");
    }
}