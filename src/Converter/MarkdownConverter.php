<?php

namespace ukatama\Mew\Converter;

use Michelf\MarkdownExtra;
use ukatama\Mew\Page;

class MewMarkdown extends MarkdownExtra {
    static public function pageTransform($text, $page, $pageStack = []) {
		$parser_class = \get_called_class();

        $parser = new $parser_class;
        $parser->page = $page;
        $parser->pageStack = $pageStack;
		return $parser->transform($text);
    }

    public function __construct() {
        $this->span_gamut += [
            'doAbbrAnchors' => 9, // Before doImages/doAnchors
            'doFileTransclusion' => 41, // After encodeAmpsAndAngles
        ];

        parent::__construct();
    }

    protected function doFileTransclusion($text) {
        $text = preg_replace_callback('/{{(.*?)}}/', function($matches) {
            $name = $matches[1];
            if (!array_key_exists($name, $this->pageStack)) {
                $page = new Page($name, 'name');
                return MewMarkdown::pageTransform($page->getHead()->getData(), $name);
            } else {
                return $matches[0];
            }
        }, $text);

        return $text;
    }

    protected function doAbbrAnchors($text) {
        return preg_replace('/\[(.*?)\](?!\()/', '[$1]($1)', $text);
    }

    protected function _checkPageLink($url) {
        return !preg_match('/^(https?)?:\/\//', $url);
    }

    protected function _doAnchors_inline_callback($matches) {
        $url = $matches[3] == '' ? $matches[4] : $matches[3];
        if ($this->_checkPageLink($url)) {
            $encoded = urlencode($url);
            $matches[3] = "?p=$encoded";
        }
        return parent::_doAnchors_inline_callback($matches);
    }

    protected function _doImages_inline_callback($matches) {
        $url = $matches[3] == '' ? $matches[4] : $matches[3];
        if ($this->_checkPageLink($url)) {
            preg_match('/^((.*)\/)?([^\/]+)$/', $url, $fmatches);

            $page = urlencode($fmatches[2] == '' ? $this->page : $fmatches[2]);
            $file = $fmatches[3];

            $matches[3] = "?a=file&p=$page&f=$file";
        }
        return parent::_doImages_inline_callback($matches);
    }
}

class MarkdownConverter extends Converter {
    private $pageStack = [];

    public function convert($src, $page = '') {
        $this->pageStack[$page] = true;
        return MewMarkdown::pageTransform($src, $page, $this->pageStack);
    }
}
