<?php

class web_app {

    public function dispatch($routes) {
        $request = $_SERVER;
        $url = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($routes as $route => $controller_class) {
            $escaped_route = str_replace('/', '\/', $route);
            $pattern = preg_replace('/:([^$\\\\]*)/', '([^$\/]*)', $escaped_route);
            $params = array();
            $match = preg_match("/^$pattern$/", $url, $params);
            if (1 === $match) {
                array_shift($params);
                $action = new ReflectionMethod($controller_class, $method);
                $action->invokeArgs(new $controller_class, $params);
                return true;
            }
        }

        throw new web_exception('Undefined route');
    }
}

class web_exception extends Exception {}
