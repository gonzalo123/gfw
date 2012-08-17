<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gfw\Db;
use Symfony\Component\HttpFoundation\Request;
use Gfw\Container;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testDbConnection()
    {
        $request   = Request::create('/index.html', 'GET');
        $container = new Container($request);
        $container->setUpViewEnvironment(__DIR__ . "/cache" . '/templates', TRUE);
        $container->setUpConfiguration(include __DIR__ . '/fixtures/Conf.php');
        $container->getView()->registerNamespace('App', __DIR__ . '/templates');

        $db = new Db($container);
        $this->assertTrue($db instanceof Db);

        $pdo = $db->getPDO('MAIN');
        $this->assertTrue($pdo instanceof \PDO);

    }
}