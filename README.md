[![Build Status](https://secure.travis-ci.org/gonzalo123/gfw.png?branch=master)](http://travis-ci.org/gonzalo123/gfw)

What is Gfw?
============

Just another micro framework for PHP

Why Gfw?
============

Because I want a micro framework to map:


```php
url: /index.html
file: /App/Index.php

<?php

namespace App;

class Index
{
    /** @GET */
    public function html()
    {
        return "Hello";
    }
}
```

How to install?
============

Install composer:
```
curl -s https://getcomposer.org/installer | php
```

Create a new project:

```
php composer.phar create-project gonzalo123/gfw gfw
```

Run dummy server (only with PHP5.4)

```
cd gfw/demo
php -S localhost:8888 www/routing.php
```

Open a web browser and type: http://localhost:8888/index.html

Framework bootstrap:

```php
// file: index.php
<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Gfw\Web;

$web = new Web(Request::createFromGlobals());
$web->setUpViewEnvironment(__DIR__ . "/../cache", TRUE);
$web->registerNamespace('App', __DIR__ . '/..');
$web->loadConfFromPath(__DIR__ . "/../Conf.php");
$web->getResponse()->send();
```

Quick examples
============

Request with paramenter (only with POST request)
===========
* url: /index.html?name=Gonzalo
* file: /App/Index.php

```php
<?php

namespace App;

class Index
{
    /** @POST */
    public function html($name)
    {
        return "Hello {$name}";
    }
}
```

Subfolders (GET and POST)
===========

* url: /application/index.html?name=Gonzalo
* file: /App/Application/Index.php

```php
<?php

namespace App\Application;

class Index
{
    /**
    * @GET
    * @POST
    */
    public function html($name)
    {
        return "Hello {$name}";
    }
}
```

Returning json
===========

* url: /index.json
* file: /App/Index.php

```php
<?php

namespace App;

class Index
{
    /** @GET */
    public function json()
    {
        return array("Hello", "World");
    }
}
```
Parameters vía Dependency injection
===========

* url: /index.html
* file: /App/Index.php

```php
<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;

class Index
{
    /** @GET */
    public function html(Request $request)
    {
        return "Hello " . $request->get('name');
    }
}
```
Parameters vía Dependency injection, but in the constructor
===========

* url: /index.html
* file: /App/Index.php

```php
<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;

class Index
{
    private $name
    public function __construct(Request $request)
    {
        $this->name = $request->get('name');
    }

    /** @GET */
    public function html(Request $request)
    {
        return "Hello {$this->name}";
    }
}
```

Twig Integration. With Dependency injection
===========

* url: /index.html
* file: /App/Index.php
* template file: /App/index.twig

```twig
Hello {{name}}
```

```php
<?php

namespace App;

use Gfw\View;

class Index
{
    /** @GET */
    public function html(View $view)
    {
        return $view->getTwig(__CLASS__)->render('index.twig', array('name' => 'Gonzalo));
    }
}
```
Twig Integration. With Dependency injection in the constructor
===========

* url: /index.html
* file: /App/Index.php
* template file: /App/index.twig

```twig
Hello {{name}}
```

```php

namespace App;

use Gfw\View;

class Index
{
    private $twig;

    public function __construct(View $view)
    {
        $this->twig = $view->getTwig(__CLASS__);
    }

    /** @GET */
    public function html()
    {
        return $this->twig->render('index.twig', array('name' => 'Gonzalo));
    }
}
```

Twig Integration within annotations
===========

* url: /index.html
* file: /App/Index.php
* template file: /App/index.twig

```twig
Hello {{name}}
```

```php
<?php

namespace App;

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
```

Db Integration
===========

We need to define the DB connections within the Conf.php file:

```php
<?php
return array(
    'DB' => array(
        'MAIN' => array(
            'dsn'      => 'sqlite::memory:',
            'username' => 'username',
            'password' => 'password',
            'options'  => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        ),
    )
);
```

* url: /index.html
* file: /App/Index.php
* template file: /App/index.twig

```twig
Hello {{name}}
```

```php
<?php

namespace App;

use Gfw\Db;

class Index
{
    /**
     * @GET
     */
    public function htm(Db $db)
    {
        $data = $db->getPDO('MAIN')->getSql()->select('users', array('id' => 1));
        return $data[0]['username'];
    }
}
```

Inject PDO with anototion
===========

* url: /index.html
* file: /App/Index.php
* template file: /App/index.twig

```twig
Hello {{name}}
```

```php
<?php

namespace App;

use Gfw\Db\PDO;

class Index
{
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
}
```

Inject Sql Object with anototion
===========

* url: /index.html
* file: /App/Index.php
* template file: /App/index.twig

```twig
Hello {{name}}
```

```php
<?php

namespace App;

use Gfw\Db\Sql;

class Index
{
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
```