<?php
use ukatama\Mew\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase {
    public function testController() {
        $c = Registry::controller('page');
        $this->assertInstanceOf('\ukatama\Mew\Controller\PageController', $c);
    }
}


