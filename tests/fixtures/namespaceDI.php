<?php

namespace App {
    use Gfw\Db;
    use Gfw\Db\PDO;
    use Gfw\Db\Sql;

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

        /**
         * @GET
         * @getPDO(db=PG, toVariable=pdo)
         * @view Index.twig
         */
        public function service(PDO $pdo)
        {
            $data = $pdo->getSql()->select('users', array('id' => 1));
            return array('name' => $data[0]['username']);
        }

        /**
         * @GET
         * @getSql(db=PG, toVariable=sql)
         * @view Index.twig
         */
        public function service2(Sql $sql)
        {
            $data = $sql->select('users', array('id' => 1));
            return array('name' => $data[0]['username']);
        }
    }
}