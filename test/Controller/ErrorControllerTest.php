<?php
use ukatama\Mew\Config;
use ukatama\Mew\Controller\ErrorController;
use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Error\PageNotFoundException;
use ukatama\Mew\Error\InternalErrorException;

class ErrorControllerTest extends PHPUnit_Framework_TestCase {
    protected $_e;

    protected function setUp() {
        $this->_e = new ErrorController();
    }

    public function testPageNotFound() {
        $this->_e->error = new PageNotFoundException('not-found-page');
        $this->_e->error();

        $this->assertEquals('pagenotfound', $this->_e->action);
        $this->assertEquals('not-found-page', $this->_e->viewVars['page']);
        $this->assertContains('"not-found-page"', $this->_e->viewVars['msg']);
    }

    public function testNotFound() {
        $this->_e->error = new NotFoundException('Error Message');
        $this->_e->error();

        $this->assertEquals('notfound', $this->_e->action);
        $this->assertEquals('Error Message', $this->_e->viewVars['msg']);
    }

    public function testForbidden() {
        $this->_e->error = new ForbiddenException('Error Message');
        $this->_e->error();

        $this->assertEquals('forbidden', $this->_e->action);
        $this->assertEquals('Error Message', $this->_e->viewVars['msg']);
    }

    public function testOther() {
        $this->_e->error = new InternalErrorException('Error Message');
        $this->_e->error();

        $this->assertEquals('error', $this->_e->action);
        $this->assertEquals('Error Message', $this->_e->viewVars['msg']);
    }
}
