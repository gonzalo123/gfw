<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gfw\Response;

use Symfony\Component\HttpFoundation\Response;

class Json extends Response
{
    public function setJsonContent($phpArray)
    {
        $this->setContent(json_encode($phpArray));
        $this->headers->set('Content-Type', 'application/json');

        return $this;
    }
}