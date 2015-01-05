<?php

use ukatama\Mew\Utility;

class UtilityTest extends PHPUnit_Framework_TestCase {
    public function testCamelCase() {
        $this->assertEquals('CamelCase', Utility::CamelCase('camelCase'));
        $this->assertEquals('CamelCase', Utility::CamelCase('Camel_case'));
        $this->assertEquals('CamelCase', Utility::CamelCase('camel_case'));
        $this->assertEquals('CamelCase', Utility::CamelCase('camel_Case'));
    }
}
