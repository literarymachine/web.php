<?php

abstract class HTTPStatus extends Exception {
    public function __toString() {
        return "HTTP/1.1 $this->code $this->message";
    }
    public function respond() {
        header($this, true, $this->code);
    }
}

abstract class HTTPRedirection extends HTTPStatus {
    protected $location = '';
    public function __construct($location) {
        $this->location = $location;
    }
    public function respond() {
        parent::respond();
        header("Location: $this->location", false);
    }
}

abstract class HTTPInformational extends HTTPStatus {}
abstract class HTTPSuccess extends HTTPStatus {}
abstract class HTTPClientError extends HTTPStatus {}
abstract class HTTPServerError extends HTTPStatus {}

class HTTP100 extends HTTPInformational {
    protected $message = 'Continue';
    protected $code = 100;
}

class HTTP101 extends HTTPInformational {
    protected $message = 'Switching Protocols';
    protected $code = 101;
}

class HTTP200 extends HTTPSuccess {
    protected $message = 'OK';
    protected $code = 200;
}

class HTTP201 extends HTTPSuccess {
    protected $message = 'Created';
    protected $code = 201;
}

class HTTP202 extends HTTPSuccess {
    protected $message = 'Accepted';
    protected $code = 202;
}

class HTTP203 extends HTTPSuccess {
    protected $message = 'Non-Athorative Information';
    protected $code = 203;
}

class HTTP204 extends HTTPSuccess {
    protected $message = 'No Content';
    protected $code = 204;
}

class HTTP205 extends HTTPSuccess {
    protected $message = 'Reset Content';
    protected $code = 205;
}

class HTTP206 extends HTTPSuccess {
    protected $message = 'Partial Content';
    protected $code = 206;
}

class HTTP300 extends HTTPRedirection {
    protected $message = 'Multiple Choice';
    protected $code = 300;
}

class HTTP301 extends HTTPRedirection {
    protected $message = 'Moved Permanently';
    protected $code = 301;
}

class HTTP302 extends HTTPRedirection {
    protected $message = 'Found';
    protected $code = 302;
}

class HTTP303 extends HTTPRedirection {
    protected $message = 'See other';
    protected $code = 303;
}

class HTTP304 extends HTTPRedirection {
    protected $message = 'Not Modified';
    protected $code = 304;
}

class HTTP305 extends HTTPRedirection {
    protected $message = 'Use Proxy';
    protected $code = 305;
}

class HTTP306 extends HTTPRedirection {
    protected $message = '(Unused)';
    protected $code = 306;
}

class HTTP307 extends HTTPRedirection {
    protected $message = 'Temporary Redirect';
    protected $code = 307;
}

class HTTP400 extends HTTPClientError {
    protected $message = 'Bad Request';
    protected $code = 400;
}

class HTTP401 extends HTTPClientError {
    protected $message = 'Unauthorized';
    protected $code = 401;
}

class HTTP402 extends HTTPClientError {
    protected $message = 'Payment Required';
    protected $code = 402;
}

class HTTP403 extends HTTPClientError {
    protected $message = 'Forbidden';
    protected $code = 403;
}

class HTTP404 extends HTTPClientError {
    protected $message = 'Not Found';
    protected $code = 404;
}

class HTTP405 extends HTTPClientError {
    protected $message = 'Method Not Allowed';
    protected $code = 405;
}

class HTTP406 extends HTTPClientError {
    protected $message = 'Not Acceptable';
    protected $code = 406;
}

class HTTP407 extends HTTPClientError {
    protected $message = 'Proxy Authentication Required';
    protected $code = 407;
}

class HTTP408 extends HTTPClientError {
    protected $message = 'Request Timeout';
    protected $code = 408;
}

class HTTP409 extends HTTPClientError {
    protected $message = 'Conflict';
    protected $code = 409;
}

class HTTP410 extends HTTPClientError {
    protected $message = 'Gone';
    protected $code = 410;
}

class HTTP411 extends HTTPClientError {
    protected $message = 'Length Required';
    protected $code = 411;
}

class HTTP412 extends HTTPClientError {
    protected $message = 'Precondition Failed';
    protected $code = 412;
}

class HTTP413 extends HTTPClientError {
    protected $message = 'Request Entity Too Large';
    protected $code = 413;
}

class HTTP414 extends HTTPClientError {
    protected $message = 'Request-URI Too Long';
    protected $code = 414;
}

class HTTP415 extends HTTPClientError {
    protected $message = 'Unsupported Media Type';
    protected $code = 415;
}

class HTTP416 extends HTTPClientError {
    protected $message = 'Request Range Not Satisfiable';
    protected $code = 416;
}

class HTTP417 extends HTTPClientError {
    protected $message = 'Expectation Failed';
    protected $code = 417;
}

class HTTP500 extends HTTPServerError {
    protected $message = 'Internal Server Error';
    protected $code = 500;
}

class HTTP501 extends HTTPServerError {
    protected $message = 'Not Implemented';
    protected $code = 501;
}

class HTTP502 extends HTTPServerError {
    protected $message = 'Bad Gateway';
    protected $code = 502;
}

class HTTP503 extends HTTPServerError {
    protected $message = 'Service Unavailable';
    protected $code = 503;
}

class HTTP504 extends HTTPServerError {
    protected $message = 'Gateway Timeout';
    protected $code = 504;
}

class HTTP505 extends HTTPServerError {
    protected $message = 'HTTP Version Not Supported';
    protected $code = 505;
}
