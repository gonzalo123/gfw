<?php

namespace App {
    use Gfw\Db;
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
        public function htm(Db $db)
        {
            $data = $db->getPDO('PG')->getSql()->select('users', array('id' => 1));
            return array('name' => $data[0]['username']);
        }
    }
}