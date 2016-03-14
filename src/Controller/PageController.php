<?php
namespace ukatama\Mew\Controller;

use ukatama\Mew\Config;
use ukatama\Mew\Controller\Controller;
use ukatama\Mew\Converter\MarkdownConverter;
use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Error\PageNotFoundException;
use ukatama\Mew\Index;
use ukatama\Mew\Input;
use ukatama\Mew\Page;

class PageController extends Controller {
    public $page;

    protected function _enforceExists() {
        if (!$this->page->exists()) {
            throw new PageNotFoundException($this->page->getName());
        }
    }

    protected function  _beforeFilter() {
        parent::_beforeFilter();
        $this->page = new Page(Input::get('p', Config::get('index')), 'name');
        $this->converter = new MarkdownConverter;
    }

    protected function _beforeRender() {
        parent::_beforeRender();

        if ($this->page) {
            $this->viewVars['page'] = $this->page->getName();
            $this->viewVars['title'] = $this->page->getName();
            if ($this->page->exists()) {
                $this->viewVars['code'] = $this->page->getHead()->getData();
                $this->viewVars['content'] = $this->converter->convert($this->viewVars['code'], $this->page->getName());
                $this->viewVars['sidebar'] = $this->converter->convert($this->sidebar->getHead()->getData(), $this->sidebar->getName());
                $this->viewVars['files'] = $this->page->getFiles();
            }
        }
    }

    public function view() {
        $this->_enforceExists();
    }

    public function add() {
        if ($this->method == 'POST' && Input::has('name')) {
            $page = new Page(Input::get('name'), 'name');
            return $this->redirect(['a' => 'edit', 'p' => $page->getName()]);
        }
    }

    public function edit() {
        if ($this->method == 'POST' && Input::has('code')) {
            $this->page->update(Input::get('code'));
            return $this->redirect(['p' => $this->page->getName()]);
        }
    }

    public function preview() {
        if ($this->method == 'POST' && Input::has('code')) {
            echo $this->converter->convert(Input::get('code'));
            return false;
        }
    }

    public function history() {
        $this->_enforceExists();

        $history = [];
        $index = $this->page->getHead();
        do {
            $timestamp = $index->getTimestamp();
            array_push($history, [
                'id' => $index->getId(),
                'name' => $index->getName(),
                'timestamp' => $timestamp
                    ? date('Y-m-d H:i:s', $timestamp)
                    : '????-??-?? ??:??:??',
            ]);
            $index = $index->getParent();
        } while ($index);
        $this->viewVars['history'] = $history;
    }

    public function histview() {
        $this->_enforceExists();

        $index = new Index(Input::get('id'));
        $timestamp = $index->getTimestamp();
        $this->viewVars['timestamp'] = $timestamp
            ? date('Y-m-d H:i:s', $timestamp)
            : null;
        $this->viewVars['data'] = $index->getData();
    }

    public function remove() {
        $this->_enforceExists();

        if ($this->method == 'POST' && Input::get('remove') === 'yes') {
            $this->page->remove();
            return $this->redirect([]);
        }
    }

    public function upload($options = []) {
        $this->_enforceExists();

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

            $id = $this->page->getId() . '_' . sha1($file['name']);
            $dst = dirname(dirname(dirname(__FILE__))) . '/file/' . $id;
            if (!move_uploaded_file($file['tmp_name'], $dst)) {
                throw new InternalErrorException;
            }

            file_put_contents($dst . '.json', json_encode($file));

            return $this->redirect('?a=edit&p=' . urlencode($this->page->name));
        }
    }

    public function file() {
        $this->_enforceExists();

        if (!Input::has('f')) {
            throw new ForbiddenException;
        }

        $name = Input::get('f');
        $id = $this->page->getId() . '_' . sha1($name);
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
