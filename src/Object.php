<?php

namespace ukatama\Mew;

use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;

class Object {
    private $_id;
    private $_data;

    public function __construct($arg, $mode = 'id') {
        if ($mode === 'data') {
            $this->_id = sha1($arg);
            $this->_data = $arg;
            if (!file_exists($this->getFilePath())) {
                file_put_contents($this->getFilePath(), $this->_data);
            }
        } else if ($mode === 'id') {
            $this->_id = $arg;
            if (!file_exists($this->getFilePath())) {
                throw new NotFoundException('Object "' . $this->_id . '" is not found');
            }
            $this->_data = file_get_contents($this->getFilePath());
        } else {
            throw new InternalErrorException;
        }
    }

    public function getId() {
        return $this->_id;
    }

    public function getFilePath() {
        return Config::get('page') . '/object/' . $this->_id;
    }

    public function getData() {
        return $this->_data;
    }
}
