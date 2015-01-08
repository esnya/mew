<?php
namespace ukatama\Mew\Controller;

use ukatama\Mew\Config;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Input;
use ukatama\Mew\Page;
use ukatama\Mew\Theme;

class Controller {
    public $theme;
    public $sidebar;

    public $action;
    public $view;
    public $method;

    public $viewVars = [];

    protected function _beforeFilter() {
    }

    protected function _beforeRender() {
    }

    public function __construct() {
        $this->theme = new Theme(Config::get('theme'));
        $this->sidebar = new Page(Config::get('sidebar'), 'name');
    }

    public function dispatch($action) {
        if (!method_exists($this, $action) || in_array($action, ['__construct', 'dispatch', 'redirect'])) {
            throw new NotFoundException("Controller doesn't has an action \"$action\"");
        }

        $this->action = $action;
        $this->view = $action;
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->_beforeFilter();

        if ($this->{$action}() !== false) {
            $this->_beforeRender();
            $this->theme->render($this->action, $this->viewVars);
        }
    }

    public function redirect($url) {
        if (is_array($url)) {
            return $this->redirect('?' . implode('&', array_map(function ($key, $value) {
                return urlencode($key) . '=' . urlencode($value);
            }, array_keys($url), array_values($url))));
        } else {
            header('Location: ' . $url, 304);
            return false;
        }
    }
}
