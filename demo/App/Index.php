<?php

namespace App;
use Gfw\View;
use Symfony\Component\HttpFoundation\Response;
use Gfw\Response\Json;

class Index
{
    /** @GET */
    public function html(View $view)
    {
        return $view->getTwig(__CLASS__)->render('index.twig', $this->json());
    }

    /** @GET */
    public function json(Json $json)
    {
        $json->setJsonContent(array(
            'name'    => 'Gonzalo',
            'surname' => 'Ayuso'
        ));
        return $json;
    }

    /** @GET */
    public function json2(Response $response)
    {
        $response->setContent(json_encode(array(
            'name'    => 'Gonzalo',
            'surname' => 'Ayuso'
        )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}