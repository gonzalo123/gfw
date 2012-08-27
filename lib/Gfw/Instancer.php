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

use Notoj\ReflectionClass;
use Gfw\Container;
use Gfw\View;
use Gfw\Parser;

class Instancer
{
    /** @var Gfw\Parser */
    private $parser;
    private $container;
    private $params;
    private $rClass;
    private $rMethod;
    private $rAnotations;
    private $isReady;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->parser    = $container->getParser();
        $this->inspectReflectionInfo();
    }

    private function inspectReflectionInfo()
    {
        try {
            $this->rClass      = new ReflectionClass($this->parser->getClassFullName());
            $this->rMethod     = $this->rClass->getMethod($this->parser->getAction());
            $this->rAnotations = $this->rMethod->getAnnotations();
            $this->params      = $this->getDecodedFunctionParameters();
            $this->isReady     = TRUE;
        } catch (\ReflectionException $e) {
            $this->isReady = FALSE;
        }
    }

    public function getInstance()
    {
        if ($this->isReady) {
            if ($this->rClass->hasMethod('__construct')) {
                $params = $this->getConstructorParams($this->rClass->getMethod('__construct'));
            } else {
                $params = array();
            }
            return $this->rClass->newInstanceArgs($params);
        } else {
            throw new Exception("Not Found", 404);
        }
    }

    private function getConstructorParams($rMethod)
    {
        $params = array();
        foreach ($rMethod->getParameters() as $param) {
            $params = $this->injectDependencies($params, $param, $rMethod);
        }
        return $params;
    }

    private function injectDependencies($params, $param, $rMethod)
    {
        $parameterName = $param->getName();
        if (isset($param->getClass()->name)) {
            switch ($param->getClass()->name) {
                case 'Symfony\Component\HttpFoundation\Request':
                    $params[$parameterName] = $this->parser->getRequest();
                    break;
                case 'Gfw\View':
                    $params[$parameterName] = $this->container->getView();
                    break;
                case 'Gfw\Db':
                    $params[$parameterName] = $this->container->getDb();
                    break;
                case 'Symfony\Component\HttpFoundation\Response':
                    $params[$parameterName] = $this->container->getResponse();
                    break;
                case 'Gfw\Response\Json':
                    $params[$parameterName] = $this->container->getJsonResponse();
                    break;
                case 'Gfw\Container':
                    $params[$parameterName] = $this->container;
                    break;
                case 'Gfw\Db\PDO':
                    $rAnnotations = $rMethod->getAnnotations();
                    if ($rAnnotations->has('getPDO')) {
                        $getPDOAnotations     = $rAnnotations->get('getPDO');
                        foreach ($getPDOAnotations as $getPDOAnotation) {
                            $dbName              = $getPDOAnotation['args']['db'];
                            $toVariable          = $getPDOAnotation['args']['toVariable'];
                            if ($toVariable == $parameterName) {
                                $params[$parameterName] = $this->container->getDb()->getPDO($dbName);
                            }
                        }
                    }
                    break;
                case 'Gfw\Db\Sql':
                    $rAnnotations = $rMethod->getAnnotations();
                    if ($rAnnotations->has('getSql')) {
                        $getSqlAnotations     = $rAnnotations->get('getSql');
                        foreach ($getSqlAnotations as $getSqlAnotation) {
                            $dbName              = $getSqlAnotation['args']['db'];
                            $toVariable          = $getSqlAnotation['args']['toVariable'];
                            if ($toVariable == $parameterName) {
                                $params[$parameterName] = $this->container->getDb()->getPDO($dbName)->getSql();
                            }
                        }
                    }
                    break;
            }
        } else {
            $parameterValue         = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : NULL;
            $params[$parameterName] = $this->parser->getParameter($parameterName, $parameterValue);
        }
        return $params;
    }

    public function invokeAction()
    {
        if ($this->isReady) {
            $requestMethod = $this->parser->getRequestMethod();
            if ($this->rAnotations->has($requestMethod)) {
                return $this->invokeFunctionWithFixtures();
            } else {
                throw new Exception("Method {$requestMethod} not allowed", 405);
            }
        } else {
            throw new Exception("Not Found", 404);
        }
    }

    private function invokeFunctionWithFixtures()
    {
        $this->processFixture('prefix');
        $out = call_user_func_array(array($this->getInstance(), $this->parser->getAction()), $this->params);
        if ($this->rAnotations->has('view')) {
            $out = $this->renderView($out);
        }
        $this->processFixture('postfix');
        return $out;
    }

    private function renderView($out)
    {
        $viewAnotation = $this->rAnotations->get('view');
        $viewAnotation = $viewAnotation[0];
        $tplName       = $viewAnotation['args'][0];
        $view          = $this->container->getView();
        return $view->getTwig($this->parser->getClassFullName())->render($tplName, $out);
    }

    private function processFixture($fixName)
    {
        if ($this->rAnotations->has($fixName)) {
            foreach ($this->rAnotations->get($fixName) as $preAnnotation) {
                $preObj    = new $preAnnotation['args']['class'];
                $preMethod = $preAnnotation['args']['method'];
                $preObj->$preMethod();
            }
        }
    }

    private function getDecodedFunctionParameters()
    {
        $params = array();
        foreach ($this->rMethod->getParameters() as $param) {
            $params = $this->injectDependencies($params, $param, $this->rMethod);
        }
        return $params;
    }
}