<?php
use ukatama\Mew\Converter\MarkdownConverter;

class MarkdownConverterTest extends PHPUnit_Framework_TestCase {
    private $_converter;

    private function convert($src, $page = '') {
        return $this->_converter->convert($src, $page);
    }

    protected function setUp() {
        $this->_converter = new MarkdownConverter;
    }

    public function testHeader() {
        $dst = $this->convert('
#Header1
##Header2
###Header3
        ');

        $this->assertContains('<h1>Header1</h1>', $dst);
        $this->assertContains('<h2>Header2</h2>', $dst);
        $this->assertContains('<h3>Header3</h3>', $dst);
    }

    public function testList() {
        $dst = $this->convert('
* list1
* list2
* list3
        ');

        $this->assertContains('<ul>', $dst);
        $this->assertContains('<li>list1</li>', $dst);
        $this->assertContains('<li>list2</li>', $dst);
        $this->assertContains('<li>list3</li>', $dst);
        $this->assertContains('</ul>', $dst);
    }

    public function testEnum() {
        $dst = $this->convert('
1. list1
1. list2
1. list3
        ');

        $this->assertContains('<ol>', $dst);
        $this->assertContains('<li>list1</li>', $dst);
        $this->assertContains('<li>list2</li>', $dst);
        $this->assertContains('<li>list3</li>', $dst);
        $this->assertContains('</ol>', $dst);
    }

    public function testLink() {
        $dst = $this->convert('[Label](http://localhost/test?q=query#hash)');

        $this->assertContains('<a ', $dst);
        $this->assertContains('href="http://localhost/test?q=query#hash"', $dst);
        $this->assertContains('</a>', $dst);
    }
    public function testMewLink() {
        $dst = $this->convert('[Label](Page/Name)');

        $this->assertContains('<a ', $dst);
        $this->assertContains('href="?p=Page%2FName"', $dst);
        $this->assertContains('Label</a>', $dst);
    }

    public function testLinkWithTitle() {
        $dst = $this->convert('[Label](http://localhost/test?q=query#hash "title")');

        $this->assertContains('<a ', $dst);
        $this->assertContains('href="http://localhost/test?q=query#hash"', $dst);
        $this->assertContains('title="title"', $dst);
        $this->assertContains('</a>', $dst);
    }
    public function testMewLinkWithTitle() {
        $dst = $this->convert('[Label](Page/Name "title")');

        $this->assertContains('<a ', $dst);
        $this->assertContains('href="?p=Page%2FName"', $dst);
        $this->assertContains('title="title"', $dst);
        $this->assertContains('Label</a>', $dst);
    }

    public function testImg() {
        $dst = $this->convert('![Label](https://localhost/image.png)');
        $this->assertContains('<img ', $dst);
        $this->assertContains('src="https://localhost/image.png"', $dst);

        $dst = $this->convert('![Label](https://localhost/image.png "title")');
        $this->assertContains('<img ', $dst);
        $this->assertContains('src="https://localhost/image.png"', $dst);
        $this->assertContains('title="title"', $dst);
    }
    public function testMewImg() {
        $dst = $this->convert('![Label](page/page/image.png)', 'page');
        $this->assertContains('<img ', $dst);
        $this->assertContains('src="?a=file&amp;p=page%2Fpage&amp;f=image.png"', $dst);
        $this->assertContains('alt="Label"', $dst);

        $dst = $this->convert('![Label](image.png)', 'page');
        $this->assertContains('<img ', $dst);
        $this->assertContains('src="?a=file&amp;p=page&amp;f=image.png"', $dst);
        $this->assertContains('alt="Label"', $dst);

        $dst = $this->convert('![Label](image.png "title")', 'page');
        $this->assertContains('<img ', $dst);
        $this->assertContains('src="?a=file&amp;p=page&amp;f=image.png"', $dst);
        $this->assertContains('title="title"', $dst);
        $this->assertContains('alt="Label"', $dst);
    }

    public function testMewShortLink() {
        $dst = $this->convert('[Page/Page]');

        $this->assertContains('<a ', $dst);
        $this->assertContains('href="?p=Page%2FPage"', $dst);
        $this->assertContains('Page/Page</a>', $dst);
    }
    public function testMewShortImg() {
        $dst = $this->convert('![image.png]', 'page');
        $this->assertContains('<img ', $dst);
        $this->assertContains('src="?a=file&amp;p=page&amp;f=image.png"', $dst);
        $this->assertContains('alt="image.png"', $dst);

        $dst = $this->convert('![page/page/image.png]', 'page');
        $this->assertContains('<img ', $dst);
        $this->assertContains('src="?a=file&amp;p=page%2Fpage&amp;f=image.png"', $dst);
        $this->assertContains('alt="page/page/image.png"', $dst);
    }

    public function testCodeBlock() {
        $dst = $this->convert(
            '#foo
            
            ```
            [Page/Page]
            ```');
        $this->assertContains('<h1>foo</h1>', $dst);
        $this->assertContains('[Page/Page]', $dst);
        $this->assertNotContains('](', $dst);
    }

    public function testInlineCode() {
        $dst = $this->convert('hogehoge`[Page/Page]`foobar');
        $this->assertContains('hogehoge<code>[Page/Page]</code>foobar', $dst);

        $dst = $this->convert('hogehoge`![Page/image.png]`foobar');
        $this->assertContains('hogehoge<code>![Page/image.png]</code>foobar', $dst);
    }

    public function testMewTransclusion() {
        $index = ukatama\Mew\Config::get('index');
        $page = new ukatama\Mew\Page($index, 'name');

        $dst = $this->convert('foo{{' . $index . '}}bar');

        $this->assertContains($this->convert($page->getHead()->getData()), $dst);
    }
}
