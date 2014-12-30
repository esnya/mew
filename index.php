<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/wiki.php';

ini_set('display_errors', $config['debug']);

use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Controller;

$controller = new Controller($config);
$page = array_key_exists('p', $_GET) ? $_GET['p'] : $config['index'];
$action = array_key_exists('a', $_GET) ? $_GET['a'] : 'view';
try {
    $controller->dispatch($page, $action);
} catch (NotFoundException $e) {
    $controller->dispatch($page, 'notfound', ['message' => $e->getMessage()]);
} catch (ForbiddenException $e) {
    $controller->dispatch($page, 'forbidden', ['message' => $e->getMessage()]);
} catch (Exception $e) {
    $controller->dispatch($page, 'error', ['message' => $e->getMessage()]);
}
