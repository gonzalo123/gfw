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

        /**
         * @GET
         * @view Index.twig
         */
        public function htm()
        {
            //$db->getPDO('MAIN');
            return array('name' => 'Gonzalo');
        }
    }
}