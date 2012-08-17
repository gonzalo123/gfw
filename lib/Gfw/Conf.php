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

use Gfw\Exception;

class Conf
{
    private $conf;

    public function __construct($conf)
    {
        $this->conf = $conf;
    }

    public function get($key)
    {
        $out = $this->conf;
        foreach (explode('.', $key) as $item) {
            if (isset($out[$item])) {
                $out = $out[$item];
            } else {
                throw new Exception("Key '{$key}' not found");
            }
        }
        return $out;
    }
}