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
use Gfw\Db\PDO;
use Gfw\Db\Sql;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testDbConnection()
    {
        $request   = Request::create('/index.html', 'GET');
        $container = $this->getContainer($request);

        $db = new Db($container);
        $this->assertTrue($db instanceof Db);

        $pdo = $db->getPDO('MAIN');
        $this->assertTrue($pdo instanceof PDO);

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS messages (
                            id INTEGER PRIMARY KEY,
                            title TEXT,
                            message TEXT)"
        );
        $pdo->exec("INSERT INTO messages(id, title, message) VALUES (1, 'title', 'message')");
        $data = $pdo->query("SELECT * FROM messages")->fetchAll();
        $this->assertEquals('title', $data[0]['title']);
    }

    public function testDbWithSqlWrapper()
    {
        $request   = Request::create('/index.html', 'GET');
        $container = new Container($request);
        $container->setUpViewEnvironment(__DIR__ . "/cache" . '/templates', TRUE);
        $container->setUpConfiguration(include __DIR__ . '/fixtures/Conf.php');
        $container->getView()->registerNamespace('App', __DIR__ . '/templates');

        $db = new Db($container);
        $this->assertTrue($db instanceof Db);

        $pdo = $db->getPDO('MAIN');
        $this->assertTrue($pdo instanceof PDO);

        $this->assertTrue($pdo->getSql() instanceof Sql);
    }

    public function testTransactional()
    {
        $request   = Request::create('/index.html', 'GET');
        $container = $this->getContainer($request);
        $db        = new Db($container);
        $pdo       = $db->getPDO('PG');
        $pdo->exec("CREATE TABLE messages(id integer, area text, username text, message text) WITH (OIDS = FALSE);");
        $pdo->transactional( function() use ($pdo) {
                $pdo->getSql()->insert(
                    'messages', array(
                        'id'       => 1,
                        'area'     => 'area',
                        'username' => 'username',
                        'message'  => 'message'
                    )
                );
            }
        );
        $pdo->exec("DROP TABLE messages");
    }

    private function getContainer($request)
    {
        $container = new Container($request);
        $container->setUpViewEnvironment(__DIR__ . "/cache" . '/templates', TRUE);
        $container->setUpConfiguration(include __DIR__ . '/fixtures/Conf.php');
        $container->getView()->registerNamespace('App', __DIR__ . '/templates');
        return $container;
    }
}