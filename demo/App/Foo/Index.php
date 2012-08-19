<?php

namespace App\Foo;
use Gfw\View;

class Index
{
    /** @GET */
    public function json()
    {
        return array(
            'name'    => 'Gonzalo',
            'surname' => 'Ayuso'
        );
    }
}