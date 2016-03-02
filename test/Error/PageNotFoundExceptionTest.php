<?php
use ukatama\Mew\Error\PageNotFoundException;

class PageNotFoundExceptionTest extends PHPUnit_Framework_TestCase {
    public function testErrorMessage() {
        $e = new PageNotFoundException('test-page');
        $this->assertEquals('A page "test-page" is not found', $e->getMessage());
        $this->assertEquals('test-page', $e->page);
    }
}
