[![Build Status](https://secure.travis-ci.org/gonzalo123/gfw.png?branch=master)](http://travis-ci.org/gonzalo123/gfw)

What is Gfw?
============

Just another micro framework for PHP

Why Gfw?
============

Because I want a micro framework to map:


```php
<?php
// url: /index.html
// file: /App/Index.php

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

Quick examples
============

Request with paramenter (only with POST request)

```php
<?php
// url: /index.html?name=Gonzalo
// file: /App/Index.php

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

```php
<?php
// url: /application/index.html?name=Gonzalo
// file: /App/Application\Index.php

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

```php
<?php
// url: /index.json
// file: /App/Index.php

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
```php
<?php
// url: /index.html
// file: /App/Index.php

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

```php
<?php
// url: /index.html
// file: /App/Index.php

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

```php
<?php
// url: /index.html
// file: /App/Index.php
/*
{# template file: /App/index.twig #}
Hello {{name}}
*/

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

```php
<?php
// url: /index.html
// file: /App/Index.php
/*
{# template file: /App/index.twig #}
Hello {{name}}
*/

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

```php
<?php
// url: /index.html
// file: /App/Index.php
/*
{# template file: /App/index.twig #}
Hello {{name}}
*/

namespace App
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

```php
<?php
// url: /index.html
// file: /App/Index.php
/*
{# template file: /App/index.twig #}
Hello {{name}}
*/

namespace App
class Index
{
    /**
     * @GET
     */
    public function htm(Db $db)
    {
        $data = $db->getPDO('PG')->getSql()->select('users', array('id' => 1));
        return $data[0]['username'];
    }
}
```

Inject PDO over anototion

```php
<?php
// url: /index.html
// file: /App/Index.php
/*
{# template file: /App/index.twig #}
Hello {{name}}
*/

namespace App
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

Inject SQLO over anototion

```php
<?php
// url: /index.html
// file: /App/Index.php
/*
{# template file: /App/index.twig #}
Hello {{name}}
*/

namespace App
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