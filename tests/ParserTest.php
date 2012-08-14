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
use Gfw\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleRequest()
    {
        $request = Request::create('/foo/var.json', 'GET');

        $parser  = new Parser($request);

        $this->assertEquals('App\Foo', $parser->getNamespace());
        $this->assertEquals('Var', $parser->getClassName());
        $this->assertEquals('GET', $parser->getRequestMethod());
        $this->assertEquals('json', $parser->getAction());
        $this->assertEquals('App\\Foo\\Var', $parser->getClassFullName());
    }

    public function testSimpleRequestWhitDifferentBaseNamespace()
    {
        $request = Request::create('/foo/var.json');
        $parser  = new Parser($request, 'Gonzalo');
        $this->assertEquals('Gonzalo\Foo', $parser->getNamespace());
        $this->assertEquals('Gonzalo\Foo\Var', $parser->getClassFullName());
    }

    public function testBigUrl()
    {
        $request = Request::create('/foo/var/x1/x2.html', 'POST');
        $parser  = new Parser($request);

        $this->assertEquals('App\\Foo\\Var\\X1', $parser->getNamespace());
        $this->assertEquals('X2', $parser->getClassName());
        $this->assertEquals('POST', $parser->getRequestMethod());
        $this->assertEquals('html', $parser->getAction());
        $this->assertEquals('App\Foo\Var\X1\X2', $parser->getClassFullName());
    }

    public function testSmallUrl()
    {
        $request = Request::create('x2.txt', 'DELETE');
        $parser  = new Parser($request);

        $this->assertEquals('App', $parser->getNamespace());
        $this->assertEquals('X2', $parser->getClassName());
        $this->assertEquals('DELETE', $parser->getRequestMethod());
        $this->assertEquals('txt', $parser->getAction());
        $this->assertEquals('App\X2', $parser->getClassFullName());
    }

    public function testRequestWithParameters()
    {
        $request = Request::create('/foo/var.json', 'GET', array('name' => 'Gonzalo', 'surname' => 'Ayuso'));
        $parser  = new Parser($request, 'Gonzalo');
        $this->assertEquals('Gonzalo\\Foo', $parser->getNamespace());
        $this->assertEquals('Gonzalo\\Foo\\Var', $parser->getClassFullName());
        $this->assertEquals('Gonzalo', $parser->getParameter('name'));
        $this->assertEquals('Ayuso', $parser->getParameter('surname'));
        $this->assertEquals('xxx', $parser->getParameter('noParameter', 'xxx'));
    }
}