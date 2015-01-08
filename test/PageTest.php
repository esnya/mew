<?php
use ukatama\Mew\Config;
use ukatama\Mew\Page;
use ukatama\Mew\Index;

class PageTest extends PHPUnit_Framework_TestCase {
    private $_backup;
    private $_pages;

    protected function setUp() {
        $this->_backup = Config::backup();
        $this->_pages = dirname(__FILE__) . '/page';
        Config::set('page', $this->_pages);
    }

    protected function tearDown() {
        Config::restore($this->_backup);

        foreach (glob($this->_pages . '/*/*') as $file) {
            unlink($file);
        }
        foreach (glob($this->_pages . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testConstructByName() {
        $name = 'TestByNameName';
        $page = new Page($name, 'name');
        $id = sha1($name);

        $this->assertEquals($id, $page->getId());
        $this->assertEquals($name, $page->getName());
    }

    public function testUpdate() {
        $name = 'TestByNameName';
        $page = new Page($name, 'name');
        $id = sha1($name);
        $data = 'TestData';

        $this->assertFalse(file_exists($this->_pages . '/' . $id));
        $page->update($data);
        $this->assertTrue(file_exists($this->_pages . '/' . $id));
    }

    public function testConstructById() {
        $name = 'PageName';
        $data = 'PageData';
        $id = sha1($name);

        (new Page($name, 'name'))->update($data);

        $page = new Page($id);

        $this->assertEquals($id, $page->getId());
        $this->assertEquals($name, $page->getName());
        $this->assertEquals($data, $page->getHead()->getData());
    }

    /**
     * @expectedException ukatama\Mew\Error\NotFoundException
     * @expectedExceptionMessage Page "invalidid" is not found
     */
    public function testErrorInvaludId() {
        new Page('invalidid');
    }

    public function testParent() {
        $parent_name = 'Name';
        $parent_data = 'ParentData';

        $parent = new Page($parent_name, 'name');
        $parent->update($parent_data);

        $name = 'Name';
        $data = 'ChildData';
        $id = sha1($name);

        (new Page($name, 'name'))->update($data);

        $page = new Page($id);

        $this->assertEquals($parent->getHead()->getId(), $page->getHead()->getParent()->getId());
    }

    public function testExists() {
        $page = new Page('PageName', 'name');
        $id = $page->getId();

        $this->assertFalse((new Page('PageName', 'name'))->exists());
        $page->update('Test');
        $this->assertTrue((new Page('PageName', 'name'))->exists());
    }
}
