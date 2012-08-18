<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gfw\Db;

use Gfw\Db\PDO;

class Sql
{
    private $pdo;
    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function select($table, $where)
    {
        $sql         = $this->createSelect($table, $where);
        $whereParams = $this->getWhereParameters($where);
        $stmp = $this->pdo->prepare($sql);
        $stmp->execute($whereParams);
        return $stmp->fetchAll();
    }

    public function insert($table, $values)
    {
        $sql = $this->createInsert($table, $values);
        $stmp = $this->pdo->prepare($sql);
        return $stmp->execute($values);
    }

    public function update($table, $values, $where)
    {
        $sql = $this->createUpdate($table, $values, $where);
        $whereParams = $this->getWhereParameters($where);

        $stmp = $this->pdo->prepare($sql);
        return $stmp->execute(array_merge($values, $whereParams));
    }

    public function delete($table, $where)
    {
        $sql         = $this->createDelete($table, $where);
        $whereParams = $this->getWhereParameters($where);
        $stmp = $this->pdo->prepare($sql);
        return $stmp->execute($whereParams);
    }

    protected function getWhereParameters($where)
    {
        $whereParams = array();
        foreach ($where as $key => $value) {
            $whereParams[":W_{$key}"] = $value;
        }
        return $whereParams;
    }

    protected function createSelect($table, $where)
    {
        return "SELECT * FROM " . $table . $this->createSqlWhere($where);
    }

    protected function createUpdate($table, $values, $where)
    {
        $sqlValues = array();
        foreach (array_keys($values) as $key) {
            $sqlValues[] = "{$key} = :{$key}";
        }
        return "UPDATE {$table} SET " . implode(', ', $sqlValues) . $this->createSqlWhere($where);
    }

    protected function createInsert($table, $values)
    {
        $sqlValues = array();
        foreach (array_keys($values) as $key) {
            $sqlValues[] = ":{$key}";
        }
        return "INSERT INTO {$table} (" . implode(', ', array_keys($values)) . ") VALUES (" . implode(', ', $sqlValues) . ")";
    }

    protected function createDelete($table, $where)
    {
        return "DELETE FROM {$table}" . $this->createSqlWhere($where);
    }

    protected function createSqlWhere($where)
    {
        if (count((array) $where) == 0) return null;

        $whereSql = array();
        foreach ($where as $key => $value) {
            $whereSql[] = "{$key} = :W_{$key}";
        }
        return ' WHERE ' . implode(' AND ', $whereSql);
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}