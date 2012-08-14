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

use Symfony\Component\HttpFoundation\Response;
use Gfw\Instancer;
use Gfw\Exception;

class Responser
{
    private $instancer;

    public function __construct(Instancer $instancer)
    {
        $this->instancer = $instancer;
    }

    /** @return \Symfony\Component\HttpFoundation\Response */
    public function getResponse()
    {
        $content = $this->getContent();
        if ($content instanceof Response) return $content;

        $response = new Response();
        if (is_array($content)) {
            $content = json_encode($content);
            $response->headers->set('Content-Type', 'application/json');
        }
        $response->setContent($content);
        return $response;
    }

    private function getContent()
    {
        try {
            return $this->instancer->invokeAction();
        } catch (Exception $e) {
            return $this->getResponseFromException($e);
        }
    }

    private function getResponseFromException($e)
    {
        $response = new Response();
        $response->setStatusCode($e->getCode());
        return $response;
    }
}
