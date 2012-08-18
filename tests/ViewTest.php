<?php

/*
 * This file is part of the Gfw package.
 *
 * (c) Gonzalo Ayuso <gonzalo123@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gfw\View;
use Gfw\Exception;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateView()
    {
        $view = new View(__DIR__ . "/templates/cache", TRUE);
        $view->registerNamespace('App', __DIR__ . '/templates');
        $twig = $view->getTwig('App\Index');
        $this->assertTrue($twig instanceof \Twig_Environment);
    }

    public function testNamespaceNotFound()
    {
        $view = new View(__DIR__ . "/templates/cache", TRUE);
        try {
            $view->registerNamespace('App1', __DIR__ . '/templates');
            $twig = $view->getTwig('App\Index');
        } catch (Exception $e) {
            $this->assertEquals("Namespace not found", $e->getMessage());
            $this->assertEquals(404, $e->getCode());
        }
    }
}