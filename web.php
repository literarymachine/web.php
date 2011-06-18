<?php

require_once('http.php');

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
        $this->_request = new WebRequest();
        $this->_response = new WebResponse();
    }

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
                    $this->_response->writeHead(501, array("Content-Type" => "text/html"));
                    $this->_response->terminate();
                }
                $action = $controller_class->getMethod($method);
                $action->invokeArgs(
                        $controller_class->newInstance($this->_request, $this->_response), $params);
            }
        }
    }

    /**
     * TODO: short description.
     * 
     * @return TODO
     */
    public function respond() {
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
     * Set up language and content negotation.
     */
    public function __construct() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->_http_accept_lang =
                $this->_parseAccept($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $this->_http_accept =
                $this->_parseAccept($_SERVER['HTTP_ACCEPT']);
        }
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $dir = dirname($_SERVER['SCRIPT_NAME']);
            $base_url = ($dir == '/') ? "" : $dir;
            $this->_base_url = $base_url;
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
     * Append to response body
     *
     * @param  string  $body
     * @return void
     */
    public function write($body) {
        $this->_body = $body;
    }

    /**
     * TODO: short description.
     * 
     * @return TODO
     */
    public function getBody() {
        return $this->_body;
    }

    /**
     * TODO: short description.
     * 
     * @return TODO
     */
    public function terminate() {
        $this->_terminated = true;
        header("HTTP/1.1 $this->_code");
        foreach ($this->_headers as $header => $value) {
            header("$header: $value");
        }
        header($header, true, $this->_code);
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
     * Set up the controller.
     *
     * @param  WebRequest  $request
     */
    public function __construct(WebRequest $request, WebResponse $response) {
        $this->_request = $request;
        $this->_response = $response;
    }

}
