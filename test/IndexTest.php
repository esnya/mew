<?php

use ukatama\Mew\Config;
use ukatama\Mew\Index;

class IndexTest extends PHPUnit_Framework_TestCase {
    private $_backup;
    private $_indices;

    protected function setUp() {
        $this->_backup = Config::backup();
        Config::set('page', dirname(__FILE__) . '/page');
        $this->_indices = dirname(__FILE__) . '/page/index';
    }

    protected function teardown() {
        Config::restore($this->_backup);
        foreach (glob($this->_indices . '/*') as $file) {
            unlink($file);
        }
    }

    public function testConstructByNameAndData() {
        $name = 'Test/PageName1';
        $data = 'TestData1';
        $index = new Index($name, $data);
        $this->assertEquals($name, $index->getName());
        $this->assertEquals($data, $index->getData());
        $this->assertTrue(file_exists($this->_indices . '/' . $index->getId()));

        $name = 'Test/PageName2';
        $data = 'TestData2';
        $index = new Index($name, $data);
        $this->assertEquals($name, $index->getName());
        $this->assertEquals($data, $index->getData());
    }

    public function testConstructByDataWithParent() {
        $parent_name = 'TestParent';
        $parent = new Index($parent_name, 'name');

        $this->assertNull($parent->getParent());

        $name = 'TestIndex';
        $index = new Index($name, 'name');

        $index->setParent($parent);

        $this->assertEquals($parent->getId(), $index->getParent()->getId());
        $this->assertEquals($parent->getName(), $index->getParent()->getName());
        $this->assertTrue(file_exists($this->_indices . '/' . $index->getId()));
    }

    public function testConstructById() {
        $name = 'Test/PageName1';
        $data = 'TestData1';
        $id = (new Index($name, $data))->getId();

        $index = new Index($id);
        $this->assertEquals($id, $index->getId());
        $this->assertEquals($name, $index->getName());
        $this->assertEquals($data, $index->getData());
    }

    public function testConstructByIdWithParent() {
        $parent_name = 'Test/Parent';
        $parent_data = 'ParentData';
        $parent= new Index($parent_name, $parent_data);

        $name = 'Test/PageName1';
        $data = 'TestData1';
        $id = (new Index($name, $data, $parent))->getId();

        $index = new Index($id);
        $this->assertEquals($id, $index->getId());
        $this->assertEquals($data, $index->getData());
        $this->assertEquals($parent->getId(), $index->getParent()->getId());
    }

    /**
     * @expectedException ukatama\Mew\Error\NotFoundException
     * @expectedExceptionMessage Index "invalidid" is not found
     */
    public function testErrorConstructWithInvalidId() {
        new Index('invalidid');
    }
}

