<?php
namespace ukatama\Mew\Controller;

use ukatama\Mew\Config;
use ukatama\Mew\Controller\Controller;
use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Error\PageNotFoundException;
use ukatama\Mew\Error\InternalErrorException;

class ErrorController extends Controller {
    public $error;

    public function error() {
        if ($this->error instanceof PageNotFoundException) {
            http_response_code(404);
            $this->action = 'pagenotfound';
            $this->viewVars['page'] = $this->error->page;
        } else if ($this->error instanceof NotFoundException) {
            http_response_code(404);
            $this->action = 'notfound';
        } else if ($this->error instanceof ForbiddenException) {
            http_response_code(403);
            $this->action = 'forbidden';
        } else {
            http_response_code(500);
            $this->action = 'error';
        }

        $this->viewVars['msg'] = $this->error->getMessage();
        if (Config::get('debug')) {
            $this->viewVars['stacktrace'] = $this->error->getTraceAsString();
        }

    }
}
