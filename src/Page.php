<?php
namespace ukatama\Mew;

use Michelf\MarkdownExtra;
use ukatama\Mew\Error\PageNotFoundException;

class Page {
    protected $_loaded;
    protected $_translated;
    protected $_markdown;
    protected $_html;
    public $name;
    public $id;
    public $filter;

    public function __construct($name, $byId = false) {
        if ($byId) {
            $this->id = $name;
        } else {
            $this->name = $name;
            $this->id = hash('sha256', $this->name);
        }
        $this->filter = Config::get('filter');
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
            $markdown = preg_replace_callback('/^Title: (.*)(\r\n|\r|\n)/m', function ($matches) {
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
        if ($this->_loaded !== $this->name) {
            throw new InternalErrorException;
        }

        if (preg_match('/\r\n/', $this->_markdown)) {
            return "\r\n";
        }
        return "\n";
    }

    public function getHTML() {
        if ($this->_translated === $this->name) {
            return $this->_html;
        } else {
            $markdown = $this->getMarkdown();

            $markdown = preg_replace_callback('/((.|\r|\n)*?)((```(.|\r|\n)*?```)|(`[^`](.|\r|\n)*?[^`]`)|$)/m', function ($matches) {
                $markdown = $matches[1];
                $markdown = preg_replace_callback('|</?(.*?)( .*?)?>|', function ($matches) {
                    if (is_array($this->filter['whitelist'])) {
                        $filtered = !in_array($matches[1], $this->filter['whitelist']);
                    } else {
                        $filtered = in_array($matches[1], $this->filter['blacklist']);
                    }

                    if ($filtered) {
                        return htmlspecialchars($matches[0]);
                    } else {
                        return $matches[0];
                    }
                }, $markdown);

                $markdown = preg_replace('/\[(.*?)\](?!\()/', '[$1]($1)', $markdown);
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

                return $markdown . $matches[3];
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
        $this->_markdown = "Title: {$this->name}{$this->getNewLine()}" . $markdown;
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
