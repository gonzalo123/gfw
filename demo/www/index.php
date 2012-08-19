<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Gfw\Web;

$web = new Web(Request::createFromGlobals());
$web->setUpViewEnvironment(__DIR__ . "/../cache", TRUE);
$web->registerNamespace('App', __DIR__ . '/..');
$web->registerConfFromPath(__DIR__ . "/../Conf.php");
$web->getResponse()->send();