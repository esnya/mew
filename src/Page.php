<?php
namespace ukatama\Mew;

use Michelf\MarkdownExtra;
use ukatama\Mew\Error\NotFoundException;

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
            if (!$this->exists()) {
                throw new NotFoundException($page->name);
            }
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
            $markdown = preg_replace_callback('/(!)?\[(.*?)\]\((.*?)\)/', function ($matches) {
                $str = $matches[2];
                $url = $matches[3];

                if (preg_match('/^(https?:\/\/|\/)/', $url)) {
                    return $matches[0];
                } else if ($matches[1]) {
                    preg_match('/^([^\/]*\/)?(.*?)( ".*")?$/', $url, $path);

                    $page = $path[1];
                    if (empty($page)) {
                        $page = $this->name;
                    } else {
                        $page = substr($page, 0, -1);
                    }
                    $page = urlencode($page);
                    $file = urlencode($path[2]);

                    $url = "?a=file&p=$page&f=$file";

                    if (count($path) == 4) {
                        $title = $path[3];
                        return "![$str]($url $title)";
                    } else {
                        return "![$str]($url)";
                    }
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
