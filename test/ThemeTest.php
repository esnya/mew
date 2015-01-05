<?php

use ukatama\Mew\Config;
use ukatama\Mew\Theme;


class ThemeTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $theme = new Theme;
        $this->assertEquals(Config::get('theme'), $theme->name);
    }

    public function testRender1() {
        $theme = new Theme;
        ob_start();
        $theme->render('test1', ['var1' => 'foobar', 'var2' => ['hoge', 'hogehoge'], 'var3' => 'bar']);
        $rendered = ob_get_clean();
        $this->assertContains('<h1>test1</h1>', $rendered);
        $this->assertContains('<li>hoge</li>', $rendered);
        $this->assertContains('<li>hogehoge</li>', $rendered);
    }

    /**
     * @expectedException   ukatama\Mew\Error\NotFoundException
     * @expectedExceptionMessage   Template "test0" is not found
     */
    public function testRenderErrorTemplateNotFound() {
        $theme = new Theme;
        $theme->render('test0');
    }
}
