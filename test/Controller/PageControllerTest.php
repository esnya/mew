<?php
use ukatama\Mew\Config;
use ukatama\Mew\Controller\PageController;
use ukatama\Mew\Converter\Converter;
use ukatama\Mew\Error\PageNotFoundException;
use ukatama\Mew\Page;

class PageControllerTest extends PHPUnit_Framework_TestCase {
    protected $controller;
    protected $page;

    protected function setUp() {
        $this->controller = new PageController();
        $this->page = $this->getMockBuilder('\ukatama\Mew\Page')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testView() {
        $this->page->method('exists')->willReturn(true);
        $this->controller->page = $this->page;

        $this->controller->view();
    }

    /**
     * @expectedException ukatama\Mew\Error\PageNotFoundException
     */
    public function testViewNotFound() {
        $this->controller->page = $this->page;
        $this->page->method('exists')->willReturn(false);
        $this->page->method('getName')->willReturn('test-page1');

        $this->controller->view();
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirectAfterAdd() {
        $this->controller->method = 'POST';
        $_REQUEST['name'] = 'test-page2';

        $this->controller->add();

        $this->assertEquals(302, http_response_code());
    }

    /**
     * @runInSeparateProcess
     */
    public function testEdit() {
        $this->controller->method = 'POST';
        $_REQUEST['code'] = 'test-page-code';

        $this->page
            ->expects($this->exactly(1))
            ->method('update')
            ->withConsecutive(['test-page-code']);

        $this->controller->page = $this->page;

        $this->controller->edit();

        $this->assertEquals(302, http_response_code());
    }

    public function testPreview() {
        $_REQUEST['code'] = 'preview-code-data';

        $converter = $this->getMock('ukatama\Converter\Converter', ['convert']);
        $converter
            ->method('convert')
            ->willReturn('converted-code-data');
        $converter
            ->expects($this->exactly(1))
            ->method('convert')
            ->withConsecutive(['preview-code-data']);
     
        $this->controller->method = 'POST';
        $this->controller->converter = $converter;

        ob_start();
        $this->controller->preview();
        $output = ob_get_clean();

        $this->assertEquals('converted-code-data', $output);
    }

    public function testRemove() {
        $_REQUEST['remove'] = 'yes';

        $this->controller->method = 'POST';
        $_REQUEST['code'] = 'test-page-code';

        $this->page
            ->expects($this->exactly(1))
            ->method('remove')
            ->withConsecutive();
        $this->page
            ->method('exists')
            ->willReturn(true);

        $this->controller->page = $this->page;

        // $this->controller->remove();
        // $this->assertEquals(302, http_response_code());

        $this->markTestIncomplete('ToDo');
    }

    public function testUpload() {
        $this->markTestIncomplete('ToDo');
    }

    public function testFile() {
        $this->markTestIncomplete('ToDo');
    }
}
