<?php

namespace App;
use Gfw\View;

class Index
{
    /** @GET */
    public function html(View $view)
    {
        return $view->getTwig(__CLASS__)->render('index.twig', $this->json());
    }

    /** @GET */
    public function json()
    {
        return array(
            'name'    => 'Gonzalo',
            'surname' => 'Ayuso'
        );
    }
}