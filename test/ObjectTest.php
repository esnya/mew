<?php

use ukatama\Mew\Config;
use ukatama\Mew\Object;

class ObjectTest extends PHPUnit_Framework_TestCase {
    private $_backup;
    private $_objects;

    protected function setUp() {
        $this->_backup = Config::backup();
        Config::set('page', dirname(__FILE__) . '/page');
        $this->_objects = dirname(__FILE__) . '/page/object';
    }

    protected function teardown() {
        Config::restore($this->_backup);
        foreach (glob($this->_objects . '/*') as $file) {
            unlink($file);
        }
    }

    public function testConstructByData() {
        $data = 'HogeHogeFoobar';
        $id = hash('sha1', $data);

        $this->assertFalse(file_exists($this->_objects . '/' . $id));

        $object = new Object($data, 'data');

        $this->assertTrue(file_exists($this->_objects . '/' . $id));
    }

    /**
     * @expectedException ukatama\Mew\Error\InternalErrorException
     */
    public function testErrorConstruction() {
        $object = new Object('invalid mode', null);
    }

    public function testGetDataById() {
        $data = 'FooFooBar';
        $id = hash('sha1', $data);
        file_put_contents(Config::get('page') . '/object/' . $id, $data);

        $object = new Object($id, 'id');

        $this->assertEquals($data, $object->getData());
    }

    /**
     * @expectedException ukatama\Mew\Error\NotFoundException
     * @expectedExceptionMessage Object "invalidid" is not found
     */
    public function testErrorGetDataWithInvalidId() {
        $object = new Object('invalidid', 'id');
    }

    public function testTimestamp() {
        $data = 'FooFooBarBar';
        $id = hash('sha1', $data);

        $file = Config::get('page') . '/object/' . $id;
        touch($file);
        $time = fileatime($file);

        $object = new Object($data, 'data');
        $this->assertEquals($time, fileatime($file));
        $this->assertEmpty(file_get_contents($file));
    }

    public function testGetData() {
        $data = 'FooBarFooBar';
        $id = hash('sha1', $data);

        $object1 = new Object($data, 'data');
        $this->assertEquals($data, $object1->getData());

        $object2 = new Object($id);
        $this->assertEquals($data, $object2->getData());
    }
}
