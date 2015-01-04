<?php
namespace ukatama\Mew\Controller;

use ukatama\Mew\Config;
use ukatama\Mew\Controller\Controller;
use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Input;
use ukatama\Mew\Page;

class PageController extends Controller {
    public $page;

    protected function  _beforeFilter() {
        parent::_beforeFilter();
        $this->page = new Page(Input::get('p', Config::get('index')));
    }

    protected function _beforeRender() {
        parent::_beforeRender();

        if ($this->page) {
            $this->viewVars['page'] = $this->page->name;
            $this->viewVars['title'] = $this->page->name;
            $this->viewVars['code'] = $this->page->getMarkdown();
            $this->viewVars['content'] = $this->page->getHTML();
            $this->viewVars['sidebar'] = $this->sidebar->getHTML();
            $this->viewVars['files'] = $this->page->getFiles();
        }
    }

    public function view() {
    }

    public function add() {
        if ($this->method == 'POST' && Input::has('name')) {
            $page = new Page(Input::get('name'));
            $page->touch();
            return $this->redirect(['a' => 'edit', 'p' => $page->name]);
        }
    }

    public function edit() {
        if ($this->method == 'POST' && Input::has('code')) {
            $this->page->setMarkdown(Input::get('code'));
            $this->page->save();
            return $this->redirect(['p' => $this->page->name]);
        }
    }

    public function preview() {
        if ($this->method == 'POST' && Input::has('code')) {
            $this->page->setMarkdown(Input::get('code'));
        }
    }

    public function remove() {
        if ($this->method == 'POST' && Input::get('remove') === 'yes') {
            $this->page->remove();
            return $this->redirect([]);
        }
    }

    public function upload($options = []) {
        if ($this->method == 'POST'&& Input::hasFile('file')) {
            $file = Input::file('file');

            if ($file['error']) {
                throw new InternalErrorException;
            }

            if (!in_array($file['type'], Config::get('filetype'))) {
                throw new ForbiddenException('Invalid file type');
            }

            if ($file['size'] > Config::get('maxsize')) {
                throw new ForbiddenException('Too large file');
            }

            if (!is_uploaded_file($file['tmp_name'])) {
                throw new ForbiddenException('Invalid file');
            }

            $id = $this->page->getHash() . '_' . hash('sha256', $file['name']);
            $dst = dirname(dirname(dirname(__FILE__))) . '/file/' . $id;
            if (!move_uploaded_file($file['tmp_name'], $dst)) {
                throw new InternalErrorException;
            }

            file_put_contents($dst . '.json', json_encode($file));

            return $this->redirect('?a=edit&p=' . urlencode($this->page->name));
        }
    }

    public function file() {
        if (!Input::has('f')) {
            throw new ForbiddenException;
        }

        $name = Input::get('f');
        $id = $this->page->getHash() . '_' . hash('sha256', $name);
        $path = dirname(dirname(dirname(__FILE__))) . '/file/' . $id;
        if (!file_exists($path)) {
            throw new NotFoundException("A file \"$name\" is not found");
        }
        $json = $path. '.json';
        if (!file_exists($json)) {
            throw new NotFoundException("Informations of the file \"$name\" are not found");
        }
        $file = json_decode(file_get_contents($json), true);

        header('Content-Type: ' . $file['type']);
        header('Content-Length: ' . $file['size']);
        header('Content-Disposition: inline; filename="' . str_replace('"', '?', $file['name']) . '"');
        echo file_get_contents($path);
        return false;
    }

}
