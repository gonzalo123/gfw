<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gfw;
use Gfw\Container;

class Db
{
    private $controller;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getPDO($key)
    {
        list($dsn, $username, $password) = $this->getConnectionCredentials($key);
        return new \PDO($dsn, $username, $password);
    }

    private function getConnectionCredentials($key)
    {
        $dbConf = $this->container->getConf()->get("DB.{$key}");

        return array($dbConf['dsn'], $dbConf['username'], $dbConf['password']);
    }
}