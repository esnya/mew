<?php
use ukatama\Mew\Page;

class PageTest extends PHPUnit_Framework_TestCase {
    private $_backup;

    private function getTestPageInfo() {
        $info = ['name' => 'PHPUnit_PageTest'];
        $info['id'] = hash('sha256', $info['name']);
        $info['file'] = dirname(dirname(__FILE__)) . '/page/' . $info['id'] . '.md';
        return $info;
    }

    protected function setUp() {
        $test_page = $this->getTestPageInfo();

        // Danger?
        if (file_exists($test_page['file'])) {
            $this->_backup = file_get_contents($test_page['file']);
            unlink($test_page['file']);
        } else {
            $this->_backup = null;
        }
    }

    protected function tearDown() {
        $test_page = $this->getTestPageInfo();

        if ($this->_backup !== null) {
            file_put_contents($test_page['file'], $this->_backup);
            $this->_backup = null;
        }
    }

    public function testConstructByName() {
        $page = new Page('PHPUnit_PageTest');

        $this->assertEquals('PHPUnit_PageTest', $page->name);
        $this->assertEquals(hash('sha256', 'PHPUnit_PageTest'), $page->id);
    }

    public function testConstructById() {
        $page = new Page(hash('sha256', 'PHPUnit_PageTest'), true);

        $this->assertEquals(hash('sha256', 'PHPUnit_PageTest'), $page->id);
    }

    public function testGetFile() {
        $page = new Page('PHPUnit_PageTest');

        $this->assertEquals('page/' . hash('sha256', 'PHPUnit_PageTest') . '.md', $page->getFile());
    }

    public function testGetMarkdown() {
        $info = $this->getTestPageInfo();

        $markdown = "# Test\r\n* Foobar";
        file_put_contents($info['file'], "Title: PHPUnit_PageTest\r\n" . $markdown);

        $page = new Page('PHPUnit_PageTest');
        $this->assertEquals($markdown, $page->getMarkdown());
    }

    public function testGetNameFromId() {
        $info = $this->getTestPageInfo();

        $markdown = "# Test\r\n* Foobar";
        file_put_contents($info['file'], "Title: TestTest\r\n" . $markdown);

        $page = new Page($info['id'], true);
        $this->assertEquals($markdown, $page->getMarkdown());
        $this->assertEquals('TestTest', $page->name);
    }

    public function testGetHtml() {
        $info = $this->getTestPageInfo();

        $markdown = "# Test\r\n* Foobar";
        file_put_contents($info['file'], "Title: PHPUnit_PageTest\r\n" . $markdown);

        $page = new Page('PHPUnit_PageTest');
        $this->assertContains('<h1>Test</h1>', $page->getHTML());
        $this->assertContains('<li>Foobar</li>', $page->getHTML());
        $this->assertContains('<li>Foobar</li>', $page->getHTML());
    }

    public function testGetFiles() {
        // ToDo
    }

    public function testSetMarkdown() {
        $info = $this->getTestPageInfo();

        $markdown1 = "# TestSetMarkdown\r\nThis is a test.";
        file_put_contents($info['file'], "Title: PHPUnit_PageTest\r\n" . $markdown1);

        $page = new Page('PHPUnit_PageTest');

        $markdown2 = "# TestSetMarkdown\r\nThis is a test.\r\nThis is a test.";
        $page->setMarkdown($markdown2);
        $this->assertEquals("Title: PHPUnit_PageTest\r\n" . $markdown2, $page->getMarkdown());

        $page->setMarkdown("Title: Test\r\n" . $markdown2);
        $this->assertEquals("Title: PHPUnit_PageTest\r\n" . $markdown2, $page->getMarkdown());
    }

    public function testReload() {
        $info = $this->getTestPageInfo();

        file_put_contents($info['file'], "Title: PHPUnit_PageTest\r\nTest1");

        $page = new Page('PHPUnit_PageTest');

        $html = $page->getHTML();
        $this->assertContains('Test1', $html);

        file_put_contents($info['file'], "Title: PHPUnit_PageTest\r\nTest2");
        $html = $page->getHTML();
        $this->assertContains('Test1', $html);

        $page->reload();

        $html = $page->getHTML();
        $this->assertContains('Test2', $html);
    }

    public function testTouch() {
        $info = $this->getTestPageInfo();

        $page = new Page('PHPUnit_PageTest');
        $this->assertFalse(file_exists($info['file']));

        $page->touch();
        $this->assertTrue(file_exists($info['file']));
    }

    public function testExists() {
        $info = $this->getTestPageInfo();

        $page = new Page('PHPUnit_PageTest');
        $this->assertFalse($page->exists());

        touch($info['file']);
        $this->assertTrue($page->exists());
    }

    public function testRemove() {
        $info = $this->getTestPageInfo();
        $page = new Page('PHPUnit_PageTest');
        touch($info['file']);
        $this->assertTrue(file_exists($info['file']));

        $page->remove();
        $this->assertFalse(file_exists($info['file']));
    }

    public function testSave() {
        $info = $this->getTestPageInfo();

        file_put_contents($info['file'], "Title: PHPUnit_PageTest\r\n#BeforeSave");

        $page = new Page('PHPUnit_PageTest');
        $page->setMarkdown('#AfterSave');

        $this->assertEquals("Title: PHPUnit_PageTest\r\n#BeforeSave", file_get_contents($info['file']));
        $page->save();
        $this->assertEquals("Title: PHPUnit_PageTest\r\n#AfterSave", file_get_contents($info['file']));
    }
}
