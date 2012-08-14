<?php
namespace App {

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    class Dummy
    {
        /** @GET */
        public function txt($name)
        {
            return View::factory(__CLASS__)->render('Index.twig', array('name' => $name));
        }
    }
}