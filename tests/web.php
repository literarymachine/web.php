<?php
require_once('../web.php');

class DispatchTest extends PHPUnit_Framework_TestCase {

    /*
     * Data provider for HTTP request methods
     */
    public function httpMethodProvider() {
        return array(
          array('GET'),
          array('POST'),
          array('PUT'),
          array('DELETE')
        );
    }

    /**
     * @expectedException HTTP404
     */
    public function testHttp404IsThrownOnEmptyMapping() {
        $_SERVER['PATH_INFO'] = '';
        $_SERVER['REQUEST_METHOD'] = '';
        $app = new WebApp();
        $app->dispatch(array(), true);
    }

    /**
     * @expectedException HTTP501
     * @dataProvider httpMethodProvider
     */
    public function testHttp501IsThrownForUnimplementedRequestMethods($method) {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['PATH_INFO'] = '/';
        $app = new WebApp();
        $app->dispatch(array('/' => 'NoMethodsControllerMock'), true);
    }

    /**
     * @dataProvider httpMethodProvider
     */
    public function testUrlParametersArePassedToController($method) {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['PATH_INFO'] = '/1';
        $app = new WebApp();
        $res = $app->dispatch(array('/:param' => 'AllMethodsControllerMock'), true);
        $this->assertEquals($res, 1);
    }

}

class AllMethodsControllerMock {
    public function GET($param) {return $param;}
    public function POST($param) {return $param;}
    public function PUT($param) {return $param;}
    public function DELETE($param) {return $param;}
}

class NoMethodsControllerMock {
}

?>
