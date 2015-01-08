<?php

use ukatama\Mew\Config;

class ConfigTest extends PHPUnit_Framework_TestCase {
    private $_backup;
    private $_config;

    protected function setUp() {
        $this->_backup = Config::backup();
        include(dirname(dirname(__FILE__)) . '/config/wiki.php');
        $this->_config = $config;
    }

    protected function tearDown() {
        Config::restore($this->_backup);
    }

    public function testGet() {
        $this->assertEquals($this->_config['index'], Config::get('index'));
    }

    public function testSet() {
        Config::set('index', 'page1');
        $this->assertEquals('page1', Config::get('index'));
        Config::set('index', 'page2');
        $this->assertEquals('page2', Config::get('index'));
    }

    public function testBackup() {
        $this->assertEquals($this->_config, Config::backup());
    }

    public function testRestore() {
        Config::set('index', 'page1');
        $backup = Config::backup();

        Config::set('index', 'page2');
        $this->assertEquals('page2', Config::get('index'));

        Config::restore($backup);
        $this->assertEquals('page1', Config::get('index'));
    }
}
