<?php
use ukatama\Mew\Converter\Converter;

class ConverterTest extends PHPUnit_Framework_TestCase {
    protected $_converter;

    protected function setUp() {
        $this->_converter = new Converter;
    }

    public function testConvert() {
        $dst = $this->_converter->convert('Test string');
        $this->assertEquals('Test string', $dst);
    }
}
