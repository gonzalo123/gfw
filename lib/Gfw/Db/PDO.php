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

use Gfw\Db\Sql;

class PDO extends \PDO
{
    public function getSql()
    {
        return new Sql($this);
    }
    public function transactional(\Closure $func)
    {
        $this->beginTransaction();
        try {
            $func($this);
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
