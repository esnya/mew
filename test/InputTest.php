<?php

use ukatama\Mew\Input;

class InputTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $_REQUEST = ['foo' => 'bar', 'hoge' => 'hogehoge'];
        $_FILES = [
            'file1' => [
                'name' => 'file1.txt',
                'type' => 'text/plain',
                'error' => 0,
                'size' => 500,
                'tmp_name' => '/tmp/file1',
            ],
        ];
    }

    public function testHas() {
        $this->assertTrue(Input::has('foo'));
        $this->assertTrue(Input::has('hoge'));

        $this->assertFalse(Input::has('foobar'));
        $this->assertFalse(Input::has('bar'));
        $this->assertFalse(Input::has('hogehoge'));
    }

    public function testGet() {
        $this->assertEquals('bar', Input::get('foo'));
        $this->assertEquals('hogehoge', Input::get('hoge'));

        $this->assertNull(Input::get('foobar'));

        $this->assertEquals('pyo', Input::get('foobar', 'pyo'));
    }

    public function testHasFile() {
        $this->assertTrue(Input::hasFile('file1'));
        $this->assertFalse(Input::hasFile('file0'));
    }

    public function testFile() {
        $file = Input::file('file1');
        $this->assertEquals('file1.txt', $file['name']);
        $this->assertEquals('text/plain', $file['type']);

        $this->assertNull(Input::file('file0'));
    }
}
