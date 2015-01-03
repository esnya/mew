<?php
namespace ukatama\Mew\Controller;

use ukatama\Mew\Controller\Controller;
use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Error\PageNotFoundException;
use ukatama\Mew\Error\InternalErrorException;

class ErrorController extends Controller {
    public $error;

    public function error() {
        if ($this->error instanceof PageNotFoundException) {
            $this->action = 'pagenotfound';
            $this->viewVars['page'] = $this->error->page;
        } else if ($this->error instanceof NotFoundException) {
            $this->action = 'notfound';
        } else if ($this->error instanceof ForbiddenException) {
            $this->action = 'forbidden';
        } else {
            $this->action = 'error';
        }

        $this->viewVars['msg'] = $this->error->getMessage();

    }
}
