<?php namespace Ashkanfekri\dodo;

class Router
{
    private $routes = [];
    public $controllerNameSpace = '';

    public function __construct($nameSpace = 'App\\Controllers\\')
    {
        $this->controllerNameSpace = $nameSpace;
    }

    //    add routes
    private function addRoute($method, $url, $action)
    {
        $url = trim($url, '/');
        $this->routes[] = [
            'method' => $method,
            'url' => $url,
            'action' => $action
        ];
    }

    public function get($url, $action)
    {
        $this->addRoute('GET', $url, $action);
    }

    public function post($url, $action)
    {
        $this->addRoute('POST', $url, $action);
    }

//    export all routes
    public function getRoutes()
    {
        return $this->routes;
    }

//    response on notfound action
    private function notFoundPage()
    {
//            action on notfound page
        return print_r("404");
    }

//    show response from route action
    private function response($response)
    {
        //show route action response
        if (is_array($response) || is_object($response) || is_bool($response) || is_string($response)) {
            return print Response::json($response);
        } else {
            return $response;
        }
    }

    public function run()
    {
        $requestUrl = (string)urldecode(Request::url());
        $requestMethod = Request::method();


        foreach ($this->routes as $route) {
            $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9الف-ی\-\_]+)', preg_quote($route['url'])) . "$@D";
            $matches = [];
            $route_data = [];
            if ($requestMethod == $route['method'] && preg_match($pattern, $requestUrl, $matches)) {
                preg_match_all('/:[a-zA-Z0-9\_\-]+/', $route['url'], $mst);
                // remove the first match
                array_shift($matches);
                // remove the first mst
                $mst = $mst[0];
//              remove : on route data key
                $mst = array_map(function ($array) {
                    return trim($array, ':');
                }, $mst);

                $route_data = array_combine($mst, $matches);

                // call the callback with the matched positions as params
                if (is_object($route['action'])) {
                    return $this->response(call_user_func_array($route['action'], $route_data));
                } else {

                    list($controller, $method) = explode('@', $route['action']);

                    $controller = ($this->controllerNameSpace . $controller);

                    if (class_exists($controller)) {
                        $controller = new $controller;

                        return $this->response(call_user_func([$controller,$method], $route_data));
                    }else{
                        throw new \Exception("کنترلر${$controller} موجود نمی باشد");
                    }
                }
            }
        }
        return $this->notFoundPage();
    }
}