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
use Gfw\Db\PDO;

class Db
{
    private $controller;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getPDO($key)
    {
        list($dsn, $username, $password, $options) = $this->getConnectionCredentials($key);
        return new PDO($dsn, $username, $password, $options);
    }

    private function getConnectionCredentials($key)
    {
        $dbConf = $this->container->getConf()->get("DB.{$key}");

        $dsn      = isset($dbConf['dsn']) ? $dbConf['dsn'] : NULL;
        $username = isset($dbConf['username']) ? $dbConf['username'] : NULL;
        $password = isset($dbConf['password']) ? $dbConf['password'] : NULL;
        $options  = isset($dbConf['options']) ? $dbConf['options'] : NULL;
        return array($dsn, $username, $password, $options);
    }
}