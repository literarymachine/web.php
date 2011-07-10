<?php

/**
 * A Web App
 */
class WebApp {

    /**
     * The WebRequest to handle.
     *
     * @var WebRequest
     */
    protected $_request;

    /**
     * Set up the app.
     */
    public function __construct() {
        $this->_request = new WebRequest($_SERVER);
        $this->_response = new WebResponse();
    }

    public function dispatch($routes) {
        $url = $this->_request->getUrl();
        $method = $this->_request->getMethod();
        foreach ($routes as $route => $controller_classname) {
            $escaped_route = str_replace('/', '\/', $route);
            $pattern = preg_replace('/:([^$\\\\]*)/', '([^$\/]+)', $escaped_route);
            $params = array();
            $match = preg_match("/^$pattern$/", $url, $params);
            if (1 === $match) {
                array_shift($params);
                $controller_class = new ReflectionClass($controller_classname);
                if (false === $controller_class->hasMethod($method)) {
                    $this->_response->writeHead(501, array("Content-Type" => "text/html"));
                    $this->_response->terminate();
                }
                $action = $controller_class->getMethod($method);
                $action->invokeArgs(
                        $controller_class->newInstance($this->_request, $this->_response, $this), $params);
                return;
            }
        }
        $this->_response->writeHead(404, array("Content-Type" => "application/xhtml+xml"));
        $this->_response->terminate();
    }

}

/**
 * A Web Request.
 */
class WebRequest {

    /**
     * The acceptable languages for this request.
     *
     * @var array
     */
    protected $_http_accept_lang = array();

    /**
     * The acceptable content-types for this request.
     *
     * @var array
     */
    protected $_http_accept = array();

    /**
     * The base url of the request.
     *
     * @var string
     */
    protected $_base_url = "";

    /**
     * The requested url.
     *
     * @var mixed  Defaults to "".
     */
    protected $_url = "";

    /**
     * The request method.
     *
     * @var mixed  Defaults to 0.
     */
    protected $_method = 0;

    /**
     * Set up language and content negotation.
     */
    public function __construct($env) {
        if (isset($env['HTTP_ACCEPT_LANGUAGE'])) {
            $this->_http_accept_lang =
                $this->_parseAccept($env['HTTP_ACCEPT_LANGUAGE']);
        }
        if (isset($env['HTTP_ACCEPT'])) {
            $this->_http_accept =
                $this->_parseAccept($env['HTTP_ACCEPT']);
        }
        if (isset($env['SCRIPT_NAME'])) {
            $dir = dirname($env['SCRIPT_NAME']);
            $base_url = ($dir == '/') ? "" : $dir;
            $this->_base_url = $base_url;
        }
        if (isset($env['PATH_INFO'])) {
            $this->_url = $env['PATH_INFO'];
        }
        if (isset($env['REQUEST_METHOD'])) {
            $this->_method = $env['REQUEST_METHOD'];
        }
    }

    /**
     * Parses an HTTP ACCEPT string into a sorted array.
     *
     * @param  string  $acceptString The string to parse.
     * @return array   Sorted array.
     */
    protected function _parseAccept($acceptString) {
        $accepts = explode(',', $acceptString);
        $qs = array();
        foreach ($accepts as $accept) {
            $q = explode(';', $accept);
            if (count($q) == 2) {
                $qs[$q[0]] = substr($q[1], 2);
            } else {
                $qs[$q[0]] = 1;
            }
        }
        arsort($qs);
        return $qs;
    }

    /**
     * Returns the acceptable languages for this request.
     *
     * @return array
     */
    public function getHttpAcceptLang() {
        return $this->_http_accept_lang;
    }

    /**
     * Returns the acceptable content-types for this request.
     *
     * @return array
     */
    public function getHttpAccept() {
        return $this->_http_accept;
    }

    /**
     * Returns the base url of the request.
     *
     * @return string
     */
    public function getBaseUrl() {
        return $this->_base_url;
    }

    /**
     * Returns the url of the request.
     *
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * Returns the request method.
     *
     * @return int
     */
    public function getMethod() {
        return $this->_method;
    }

}

/**
 * A Web Response
 */
class WebResponse {

    /**
     * Header string
     *
     * @var string  Defaults to array().
     */
    protected $_headers = array();

    /**
     * HTTP status code
     *
     * @var int  Defaults to 0.
     */
    protected $_code = 0;

    /**
     * Response body
     *
     * @var string  Defaults to "".
     */
    protected $_body = "";

    /**
     * Whether the response has been terminated.
     *
     * @var bool  Defaults to false.
     */
    protected $_terminated = false;

    /**
     * Set the response header.
     *
     * @param  int    $code   HTTP status code
     * @param  array  $headers Header string
     * @return void
     */
    public function writeHead($code, $headers) {
        if ($this->_terminated) {
            throw new Exception("Trying to write to terminated response.");
        }
        $this->_code = $code;
        $this->_headers = $headers;
    }

    /**
     * Write to response body
     *
     * @param  string  $body
     * @return void
     */
    public function write($body) {
        $this->_body = $body;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody() {
        return $this->_body;
    }

    /**
     * Trigger the response
     *
     * @return void
     */
    public function terminate() {
        $this->_terminated = true;
        header("HTTP/1.1 $this->_code");
        foreach ($this->_headers as $header => $value) {
            header("$header: $value");
        }
        echo $this->_body;
    }
}

/**
 * A Web Controller
 */
class WebController {

    /**
     * The WebRequest to handle.
     *
     * @var WebRequest
     */
    protected $_request;

    /**
     * The WebResponse to trigger.
     *
     * @var WebResponse;
     */
    protected $_response;

    /**
     * The WebApp calling the controller
     *
     * @var WebApp;
     */
    protected $_app;

    /**
     * Set up the controller.
     *
     * @param  WebRequest  $request
     */
    public function __construct(WebRequest $request, WebResponse $response, WebApp $app) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_app = $app;
    }

}
