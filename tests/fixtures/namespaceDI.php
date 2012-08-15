<?php

namespace App {
    class Index
    {
        /**
         * @GET
         * @view Index.twig
         */
        public function html()
        {
            return array('name' => 'Gonzalo');
        }
    }
}