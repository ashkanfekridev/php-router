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
        return print_r("صفحه مورد نظر شما صحیح نیست!");
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
        $requestUrl = (string) urldecode(Request::url());
        $requestMethod = Request::method();


        foreach ($this->routes as $route) {
            $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9الف-ی\-\_]+)', preg_quote($route['url'])) . "$@D";
            $matches = [];
            if ($requestMethod == $route['method'] && preg_match($pattern, $requestUrl, $matches)) {
                preg_match_all('/:[a-zA-Z0-9\_\-]+/', $route['url'], $mst);
                // remove the first match
                array_shift($matches);
                // call the callback with the matched positions as params
                if (is_object($route['action'])) {
                    $callAction = call_user_func_array($route['action'], $matches);
                } else {
                    $action = explode('@', $route['action']);
                    $callAction = call_user_func([$this->controllerNameSpace . $action[0], $action[1]], $matches);
                }
                return $this->response($callAction);
            }
        }
        return $this->notFoundPage();
    }
}