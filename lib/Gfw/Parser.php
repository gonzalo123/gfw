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

use Symfony\Component\HttpFoundation\Request;

class Parser
{
    private $request;
    private $namespace;
    private $className;
    private $requestMethod;
    private $action;
    private $baseNamespace;
    private $classFullName;

    public function __construct(Request $request, $baseNamespace = 'App')
    {
        $this->request       = $request;
        $this->baseNamespace = $baseNamespace;
        $this->decodePathInfo();
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getClassFullName()
    {
        return $this->classFullName;
    }

    public function getReflectionClass()
    {
        return new \ReflectionClass($this->parser->getClassFullName());
    }

    public function getParameter($key, $default = null)
    {
        return $this->request->get($key, $default);
    }

    private function decodePathInfo()
    {
        $conf = array($this->baseNamespace);
        $arr  = explode('/', dirname($this->request->getPathInfo()));
        for ($i = 0; $i < count($arr); $i++) {
            $elem = $arr[$i];
            if (strpos($elem, '.') !== FALSE) {
                list($functionName, $format) = explode(".", $elem);
                continue;
            } else {
                if ($elem != '') {
                    $conf[] = ucfirst($elem);
                }
            }
        }
        $this->namespace = implode('\\', $conf);

        list($className, $this->action) = explode('.', basename($this->request->getPathInfo()));
        $this->className     = ucfirst($className);
        $this->requestMethod = $this->request->getMethod();
        $this->classFullName = $this->namespace != '' ? $this->namespace . '\\' . $this->className : $this->className;
    }
}