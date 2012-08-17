<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gfw\Conf;

class ConfTest extends \PHPUnit_Framework_TestCase
{
    public function testConfLoad()
    {
        $conf = include_once __DIR__ . '/fixtures/Conf.php';
        $conf = new Conf($conf);
        $this->assertEquals('sqlite::memory:', $conf->get('DB.MAIN.dsn'));
        $this->assertEquals('username', $conf->get('DB.MAIN.username'));
        $this->assertEquals(
            array(
                'dsn'      => 'sqlite::memory:',
                'username' => 'username',
                'password' => 'password'
            ), $conf->get('DB.MAIN')
        );
        //$this->assertTrue($db instanceof Db);

        //$pdo = $db->getPDO('MAIN');
        //$this->assertTrue($pdo instanceof \PDO);
    }
}