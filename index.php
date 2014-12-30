<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/wiki.php';

ini_set('display_errors', $config['debug']);

use \Michelf\MarkdownExtra;
use ukatama\Wiki\Error\ForbiddenException;
use ukatama\Wiki\Error\InternalErrorException;
use ukatama\Wiki\Error\NotFoundException;
use ukatama\Wiki\Page;
use ukatama\Wiki\Theme;

function redirect($url) {
    header('Location: ' . $url, true, 301);
    exit();
}

try {
    $theme = new Theme($config['theme']);
    $page = new Page(array_key_exists('p', $_GET) ? $_GET['p'] : $config['index']);
    $sidebar = new Page($config['sidebar']);

    $action = 'view';
    if (array_key_exists('a', $_GET)) {
        $action = $_GET['a'];
    }

    if ($action == 'add'
        && $_SERVER['REQUEST_METHOD'] === 'POST'
        && array_key_exists('name', $_POST)) {
        redirect('?a=edit&p=' . urlencode($_POST['name']));
    }

    if ($action == 'edit' && !$page->exists()) {
        $page->touch();
    }

    if ($action == 'edit'
        && $_SERVER['REQUEST_METHOD'] === 'POST'
        && array_key_exists('code', $_POST)) {
        file_put_contents($page->getFile(), $_POST['code']);
        //$page->reload();
        redirect('?p=' . urlencode($page->name));
    } else if ($action == 'preview'
        && $_SERVER['REQUEST_METHOD'] === 'POST'
        && array_key_exists('code', $_POST)) {
        $page->setMarkdown($_POST['code']);
    } else if ($action == 'remove'
        && $_SERVER['REQUEST_METHOD'] === 'POST'
        && array_key_exists('remove', $_POST)
        && $_POST['remove'] == 'yes') {
        $page->remove();
        redirect('?p=index');
    } else if ($action == 'upload'
        && $_SERVER['REQUEST_METHOD'] === 'POST'
        && array_key_exists('file', $_FILES)
    ) {
        $file = $_FILES['file'];

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

        $id = $page->getHash() . '_' . hash('sha256', $file['name']);
        $dst = dirname(__FILE__) . '/file/' . $id;
        if (!move_uploaded_file($file['tmp_name'], $dst)) {
            throw new InternalErrorException;
        }

        file_put_contents($dst . '.json', json_encode($file));

        redirect('?a=edit&p=' . urlencode($page->name));
    } else if($action == 'file') {
        if (!array_key_exists('f', $_GET)) {
            throw new ForbiddenException;
        }

        $id = $page->getHash() . '_' . hash('sha256', $_GET['f']);
        $path = dirname(__FILE__) . '/file/' . $id;
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
        exit();
    }

    $variables = [
        'page' => $page->name,
        'title' => $page->name,
        'code' => $page->getMarkdown(),
        'content' => $page->getHTML(),
        'sidebar' => $sidebar->getHTML(),
        'files' => $page->getFiles(),
    ];

    $theme->render($action, $variables);
} catch (NotFoundException $e) {
    $theme->render('notfound', [
        'page' => $page->name,
        'title' => $page->name,
        'message' => $e->getMessage(),
    ]);
} catch (Exception $e) {
    $theme->render('error', [
        'page' => $page->name,
        'title' => $page->name,
        'message' => $e->getMessage(),
    ]);
}
