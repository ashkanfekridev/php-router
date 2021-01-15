# php-router

this is a php-mvc router

------
## install 

```console
composer require ashkanfekri/router
```

------
## using 

### create new route

```php
<?php
use Ashkanfekri\dodo\Router;

$router = new Request();

$router->get('/', "ControllerName@MethodName");
$router->post('/', "ControllerName@MethodName");
```


```php
<?php

use Ashkanfekri\dodo\Router;
     
$router = new Request();

$router->get('post/:slug', "PostController@show");

class PostController{
    public function show($slug){
        return $slug;
    }
}



```