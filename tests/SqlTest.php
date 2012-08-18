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
use Gfw\Db\Sql;
use Gfw\Db\PDO;

class SqlTest extends \PHPUnit_Framework_TestCase
{
    private $pdo;
    public function setUp()
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS users (
                                    uid INTEGER PRIMARY KEY,
                                    name TEXT,
                                    surname TEXT)");
    }

    public function testOperations()
    {
        $sql = new Sql($this->pdo);

        $actual = $sql->insert('users', array('uid' => 7, 'name' => 'Gonzalo', 'surname' => 'Ayuso'));
        $this->assertTrue($actual);

        $actual = $sql->insert('users', array('uid' => 8, 'name' => 'Peter', 'surname' => 'Parker'));
        $this->assertTrue($actual);

        $data = $sql->select('users', array('uid' => 8));
        $this->assertEquals('Peter', $data[0]['name']);
        $this->assertEquals('Parker', $data[0]['surname']);

        $sql->update('users', array('name' => 'gonzalo'), array('uid' => 7));

        $data = $sql->select('users', array('uid' => 7));
        $this->assertEquals('gonzalo', $data[0]['name']);

        $data = $sql->delete('users', array('uid' => 7));

        $data = $sql->select('users', array('uid' => 7));
        $this->assertTrue(count($data) == 0);

        $this->assertTrue($sql->getPDO() instanceof PDO);
    }
}