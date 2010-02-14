<?php

class web_app {

    public function dispatch($routes) {
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
                    throw new web_exception("Method $method not implemented.");
                }
                $action = $controller_class->getMethod($method);
                $action->invokeArgs($controller_class->newInstance(), $params);
                return;
            }
        }

        throw new web_exception("Unmatched route: $url");
    }
}

class web_exception extends Exception {}
