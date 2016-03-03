<?php

use ukatama\Mew\Config;
use ukatama\Mew\Controller\Controller;

class TestController extends Controller {
    public $test1 = 0;

    public function test1() {
        ++$this->test1;
    }
}

class ControllerTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $ctr = new Controller;
        $this->assertEquals(Config::get('theme'), $ctr->theme->name);
        $this->assertEquals(Config::get('sidebar'), $ctr->sidebar->getName());
    }

    public function testDispatch() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $ctr = new TestController;
        $ctr->theme = $this->getMock('Theme', ['render']);
        $ctr->theme->expects($this->once())->method('render');
        $ctr->dispatch('test1');

        $this->assertEquals(1, $ctr->test1);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirect() {
        $ctr = new TestController;

        $this->assertFalse($ctr->redirect(['a' => 'view', 'c' => 'test', 'p' => 'test1']));

        $this->assertEquals(302, http_response_code());

        $headers = xdebug_get_headers();
        $this->assertContains('Location: ?a=view&c=test&p=test1', $headers);
    }
}
