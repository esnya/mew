<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/wiki.php';

ini_set('display_errors', $config['debug']);

use \Michelf\MarkdownExtra;
use ukatama\Wiki\Theme;
use ukatama\Wiki\Page;
use ukatama\Wiki\Error\NotFoundException;

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
        $add = new Page($_POST['name']);
        $add->touch();
        redirect('?p=' . urlencode($add->name));
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
    }

    $variables = [
        'page' => $page->name,
        'title' => $page->name,
        'code' => $page->getMarkdown(),
        'content' => $page->getHTML(),
        'sidebar' => $sidebar->getHTML(),
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