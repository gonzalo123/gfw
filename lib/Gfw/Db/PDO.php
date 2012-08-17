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

class PDO extends \PDO
{
    private $forcedRollback = false;
    public function transactional(Closure $func)
    {
        $this->beginTransaction();
        try {
            $func($this);
            $this->forcedRollback ? $this->rollback() : $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function forceRollback()
    {
        $this->forcedRollback = true;
    }
}
