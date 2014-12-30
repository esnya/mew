<?php
namespace ukatama\Mew;

use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Page;

class Controller {
    public $filter;
    public $theme;
    public $sidebar;

    public $action;
    public $view;
    public $method;
    public $data;

    public function __construct($config = []) {
        $this->filter = $config['tag'];

        $this->theme = new Theme($config['theme']);
        $this->sidebar = new Page($config['sidebar'], $this->filter);
    }

    public function dispatch($page, $action, $variables = []) {
        if (!method_exists($this, $action) || in_array($action, ['__construct', 'dispatch', 'redirect'])) {
            throw new NotFoundException("Controller doesn't has an action \"$action\"");
        }

        $this->action = $action;
        $this->view = $action;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->data = $_POST;
        $this->files = $_FILES;
        $this->page = new Page($page, $this->filter);
        $options = $_GET;

        if ($this->{$action}($options) !== false) {
            if ($this->page) {
                $variables['page'] = $this->page->name;
                $variables['title'] = $this->page->name;
                $variables['code'] = $this->page->getMarkdown();
                $variables['content'] = $this->page->getHTML();
                $variables['sidebar'] = $this->sidebar->getHTML();
                $variables['files'] = $this->page->getFiles();
            }

            $this->theme->render($this->action, $variables);
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

    public function notfound() {
        $this->page = null;
    }
    public function forbidden() {
        $this->page = null;
    }
    public function error() {
        $this->page = null;
    }

    public function view($options = []) {
    }

    public function add() {
        if ($this->method == 'POST' && array_key_exists('name', $this->data)) {
            $page = new Page($this->data['name'], $this->filter);
            $page->touch();
            return $this->redirect(['a' => 'edit', 'p' => $page->name]);
        }
    }

    public function edit($options = []) {
        if ($this->method == 'POST' && array_key_exists('code', $this->data)) {
            file_put_contents($this->page->getFile(), $this->data['code']);
            return $this->redirect(['p' => $this->page->name]);
        }
    }

    public function preview($options = []) {
        if ($this->method == 'POST' && array_key_exists('code', $this->data)) {
            $this->page->setMarkdown($this->data['code']);
        }
    }

    public function remove($options = []) {
        if ($this->method == 'POST' && array_key_exists('remove', $this->data) && $this->data['remove'] === 'yes') {
            $this->page->remove();
            return $this->redirect([]);
        }
    }

    public function upload($options = []) {
        if ($this->method == 'POST'&& array_key_exists('file', $this->files)) {
            $file = $this->files['file'];

            if ($file['error']) {
                throw new InternalErrorException;
            }

            if (!in_array($file['type'], $config['file']['type'])) {
                throw new ForbiddenException('Invalid file type');
            }

            if ($file['size'] > $config['file']['max_size']) {
                throw new ForbiddenException('Too large file');
            }

            if (!is_uploaded_file($file['tmp_name'])) {
                throw new ForbiddenException('Invalid file');
            }

            $id = $this->page->getHash() . '_' . hash('sha256', $file['name']);
            $dst = dirname(dirname(__FILE__)) . '/file/' . $id;
            if (!move_uploaded_file($file['tmp_name'], $dst)) {
                throw new InternalErrorException;
            }

            file_put_contents($dst . '.json', json_encode($file));

            return $this->redirect('?a=edit&p=' . urlencode($this->page->name));
        }
    }

    public function file($options = []) {
        if (!array_key_exists('f', $options)) {
            throw new ForbiddenException;
        }

        $id = $this->page->getHash() . '_' . hash('sha256', $options['f']);
        $path = dirname(dirname(__FILE__)) . '/file/' . $id;
        if (!file_exists($path)) {
            throw new NotFoundException;
        }
        $json = $path. '.json';
        if (!file_exists($json)) {
            throw new NotFoundException;
        }
        $file = json_decode(file_get_contents($json), true);

        header('Content-Type: ' . $file['type']);
        header('Content-Length: ' . $file['size']);
        header('Content-Disposition: inline; filename="' . str_replace('"', '?', $file['name']) . '"');
        echo file_get_contents($path);
        return false;
    }
}
