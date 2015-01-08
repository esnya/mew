<?php

namespace ukatama\Mew;

use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Object;

class Index {
    private $_id;
    private $_name;
    private $_data_id = null;
    private $_data = null;
    private $_parent_id = null;
    private $_parent = null;

    public function __construct($one, $two = null, $three = null) {
        if ($two === null) {
            $this->_id = $one;
            if (!file_exists($this->getFilePath())) {
                throw new NotFoundException('Index "' . $this->_id . '" is not found');
            }

            $json = json_decode(file_get_contents($this->getFilePath()));

            $this->_name = $json->name;
            $this->_data_id = $json->data;
            $this->_parent_id = $json->parent;
        } else {
            $this->_id = sha1(sha1($one) . sha1($two) . sha1(time()) . sha1(rand()));
            $this->_name = $one;

            if ($two instanceof Object) {
                $this->_data = $two;
            } else {
                $this->_data = new Object($two, 'data');
            }
            $this->_data_id = $this->_data->getId();

            $this->setParent($three);

            if (file_exists($this->getFilePath())) {
                throw new InternalErrorException;
            }

            file_put_contents($this->getFilePath(), json_encode([
                'name' => $this->_name,
                'data' => $this->_data->getId(),
                'parent' => $this->_parent_id,
            ]));
        }
    }

    public function getId() {
        return $this->_id;
    }

    public function getFilePath() {
        return Config::get('page') . '/index/' . $this->_id;
    }

    public function getName() {
        return $this->_name;
    }

    public function getParent() {
        if (!$this->_parent && $this->_parent_id) {
            $this->_parent = new Index($this->_parent_id);
        }
        return $this->_parent;
    }
    public function setParent($parent) {
        if ($parent instanceof Index) {
            $this->_parent = $parent;
            $this->_parent_id = $parent->getId();
        } else {
            $this->_parent_id = $parent;
        }
    }

    public function getData() {
        if (!$this->_data) {
            $this->_data = new Object($this->_data_id, 'id');
        }
        return $this->_data->getData();
    }
}
