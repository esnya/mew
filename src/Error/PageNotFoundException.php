<?php
namespace ukatama\Mew\Error;

use ukatama\Mew\Error\NotFoundException;

class PageNotFoundException extends NotFoundException {
    public $page;

    public function __construct($page) {
        parent::__construct("A page \"$page\" is not found");
        $this->page = $page;
    }
}
