<?php
namespace ukatama\Mew;

use Michelf\MarkdownExtra;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Error\PageNotFoundException;
use ukatama\Mew\Converter\MarkdownConverter;

class Page {
    protected $_id;
    protected $_name = null;
    protected $_index = null;
    protected $_index_id = null;

    public function __construct($arg, $mode = 'id') {
        if ($mode === 'id') {
            $this->_id = $arg;
            if (!file_exists($this->getFileName())) {
                throw new NotFoundException('Page "' . $this->_id . '" is not found');
            }
            $this->_load();
        } else if ($mode === 'name') {
            $this->_id = sha1($arg);
            $this->_name = $arg;

            if (file_exists($this->getFileName())) {
                $this->_load();
            }
        } else {
            throw new InternalErrorException;
        }
    }

    protected function _load() {
        $json = json_decode(file_get_contents($this->getFileName()));

        if ($this->_name && $this->_name != $json->name) {
            throw new InternalErrorException;
        }

        if ($json) {
            $this->_name = $json->name;
            $this->_index_id = $json->index;
        }
    }

    public function getId() {
        return $this->_id;
    }

    public function getFileName() {
        return Config::get('page') . '/' . $this->getId();
    }

    public function getName() {
        return $this->_name;
    }

    public function getHead() {
        if (!$this->_index && $this->_index_id) {
            $this->_index = new Index($this->_index_id);
        }
        return $this->_index;
    }

    public function update($data) {
        $this->_index = new Index($this->getName(), $data, $this->_index_id);
        $this->_index_id = $this->_index->getId();
        file_put_contents($this->getFileName(), json_encode([
            'name' => $this->getName(),
            'index' => $this->_index_id,
        ]));
    }

    public function exists() {
        return file_exists($this->getFileName());
    }

    public function getFiles() {
        $ptn = Config::get('file') . '/' . $this->getId() . '_*.json';
        return array_map(function ($json) {
            $file = json_decode(file_get_contents($json), true);
            $p = urlencode($this->getName());
            $f = urlencode($file['name']);
            $file['url'] = "?a=file&p=$p&f=$f";
            return $file;
        }, glob($ptn));
    }

    static function getPages() {
        return array_map(function ($file) {
            return new Page(basename($file));
        }, array_filter(glob(Config::get('page') . '/*'), 'is_file'));
    }
}
