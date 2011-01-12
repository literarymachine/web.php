<?php

require_once('http.php');

class WebApp {

    public function dispatch($routes, $exceptions = false) {
        $url = $_SERVER['PATH_INFO'];
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($routes as $route => $controller_classname) {
            $escaped_route = str_replace('/', '\/', $route);
            $pattern = preg_replace('/:([^$\\\\]*)/', '([^$\/]+)', $escaped_route);
            $params = array();
            $match = preg_match("/^$pattern$/", $url, $params);
            if (1 === $match) {
                array_shift($params);
                $controller_class = new ReflectionClass($controller_classname);
                if (false === $controller_class->hasMethod($method)) {
                    $http_status = new HTTP501;
                    if ($exceptions) throw $http_status;
                    else return $http_status->respond();
                }
                $action = $controller_class->getMethod($method);
                try {
                    return $action->invokeArgs($controller_class->newInstance(), $params);
                } catch (HTTPStatus $http_status) {
                    if ($exceptions) throw $http_status;
                    else return $http_status->respond();
                }
            }
        }

        $http_status = new HTTP404;
        if ($exceptions) throw $http_status;
        else return $http_status->respond();
    }
}

