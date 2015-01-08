<?php
namespace ukatama\Mew;

use Michelf\MarkdownExtra;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\PageNotFoundException;
use ukatama\Mew\Converter\MarkdownConverter;

class Page {
    protected $_loaded;
    protected $_translated;
    protected $_markdown;
    protected $_html;
    public $name;
    public $id;
    public $filter;
    public $converter;

    public function __construct($name, $byId = false) {
        if ($byId) {
            $this->id = $name;
        } else {
            $this->name = $name;
            $this->id = hash('sha256', $this->name);
        }
        $this->filter = Config::get('filter');
        $this->converter = new MarkdownConverter;
        $this->reload();
    }

    public function getHash() {
        return $this->id;
    }

    public function getFile() {
        return 'page/' . $this->getHash() . '.md';
    }

    public function getMarkdown() {
        if ($this->_loaded === $this->name) {
            return $this->_markdown;
        } else {
            if (!$this->exists()) {
                throw new PageNotFoundException($this->name);
            }
            $markdown = file_get_contents($this->getFile());
            $markdown = preg_replace_callback('/^Title: (.*?)(\r\n|\r|\n)/', function ($matches) {
                $name = $matches[1];
                if ($this->name) {
                    if ($this->name !== $name) {
                        throw new InternalErrorException;
                    }
                } else {
                    $this->name = $name;
                }
                return '';
            }, $markdown);
            $this->_markdown = $markdown;
            $this->_loaded = $this->name;
            return $this->_markdown;
        }
    }

    public function getNewLine() {
        return "\r\n";
    }

    public function getHTML() {
        if ($this->_translated === $this->name) {
            return $this->_html;
        } else {
            $markdown = $this->getMarkdown();

            $this->_html = $this->converter->convert($markdown, $this->name);

            $this->_translated = $this->name;
            return $this->_html;
        }
    }

    public function getFiles() {
        $ptn = dirname(dirname(__FILE__)) . '/file/' . $this->getHash() . '_*.json';
        return array_map(function ($json) {
            $file = json_decode(file_get_contents($json), true);
            $p = urlencode($this->name);
            $f = urlencode($file['name']);
            $file['url'] = "?a=file&p=$p&f=$f";
            return $file;
        }, glob($ptn));
    }

    public function setMarkdown($markdown) {
        $this->_translated = false;
        $this->_loaded = $this->name;
        $this->_markdown = "Title: {$this->name}{$this->getNewLine()}" . preg_replace('/^Title: (.*?)(\r\n|\r|\n)/', '', $markdown);
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

    public function save() {
        if ($this->_loaded !== $this->name) {
            throw new InternalErrorException;
        }
        file_put_contents($this->getFile(), $this->_markdown);
    }
}
