<?php
namespace ukatama\Wiki;

use Michelf\MarkdownExtra;

class Page {
    protected $_loaded;
    protected $_translated;
    protected $_markdown;
    protected $_html;
    public $name;

    public function __construct($name) {
        $this->name = $name;
        $this->reload();
    }

    public function getHash() {
        return hash('sha256', $this->name);
    }

    public function getFile() {
        return 'page/' . $this->getHash() . '.md';
    }

    public function getMarkdown() {
        if ($this->_loaded === $this->name) {
            return $this->_markdown;
        } else {
            $this->_markdown = file_get_contents($this->getFile());
            $this->_loaded = $this->name;
            return $this->_markdown;
        }
    }

    public function getHTML() {
        if ($this->_translated === $this->name) {
            return $this->_html;
        } else {
            $markdown = preg_replace('/\[(.*?)\](?!\()/', '[$1]($1)', $this->getMarkdown());
            $markdown = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function ($matches) {
                $str = $matches[1];
                $url = $matches[2];

                if (preg_match('/^(https?:\/\/|\/)/', $url)) {
                    return $matches[0];
                } else {
                    $encoded = urlencode($url);
                    return "[$str](?p=$encoded)";
                }
            }, $markdown);
            $this->_html = MarkdownExtra::defaultTransform($markdown);
            $this->_translated = $this->name;
            return $this->_html;
        }
    }

    public function setMarkdown($markdown) {
        $this->_translated = false;
        $this->_loaded = $this->name;
        $this->_markdown = $markdown;
    }

    public function reload() {
        $this->_loaded = false;
        $this->_translated = false;
    }

    public function touch() {
        file_put_contents($this->getFile(), '');
    }

    public function exists() {
        return file_exists($this->getFile());
    }

    public function remove() {
        unlink($this->getFile());
        $this->reload();
    }
}
