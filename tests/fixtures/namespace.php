<?php

namespace App\Foo {

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    class Dummy
    {
        /** @GET */
        public function json()
        {
            return array('Hi', 'Gonzalo');
        }

        /** @GET */
        public function txt($name)
        {
            return "Hi {$name}";
        }

        /** @GET */
        public function html(Request $request)
        {
            return "Hi " . $request->get('name');
        }

        /** @GET */
        public function action()
        {
            return new Response();
        }
    }

    class Dummy2
    {
        private $request;

        function __construct(Request $request)
        {
            $this->request = $request;
        }

        /** @GET */
        public function html()
        {
            return "Hi " . $this->request->get('name');
        }
    }
}

namespace App\Annotations {

    class Dummy
    {
        /**
         * @POST
         */
        public function json($name)
        {
            return "Hi";
        }

        /**
         * @GET
         * @postfix(class=\App\Logger, method=logAction)
         */
        public function action()
        {
            return "Hi";
        }

        /**
         * @GET
         * @postfix(class=\App\Logger, method=logAction)
         * @postfix(class=\App\Logger, method=logAction2)
         */
        public function action2()
        {
            return "Hi";
        }

        /**
         * @GET
         * @prefix(class=\App\Auth, method=checkAuth)
         */
        public function withAuth()
        {
            return "Hi";
        }
    }
}

namespace App {
    use Gfw\Exception;

    class Auth
    {
        public function checkAuth()
        {
            throw new Exception("Unauthorized", 401);
        }
    }

    class Logger
    {
        public static $flag = 0;

        public function logAction()
        {
            self::$flag++;
        }

        public function logAction2()
        {
            self::$flag++;
            self::$flag++;
        }
    }
}