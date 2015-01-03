<?php
require_once __DIR__ . '/vendor/autoload.php';

use ukatama\Mew\Config;
use ukatama\Mew\Error\ForbiddenException;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Error\NotFoundException;
use ukatama\Mew\Input;
use ukatama\Mew\PageController;

ini_set('display_errors', Config::get('debug'));

$controller = new PageController();
$page = Input::get('p');
$action = Input::get('a', 'view');
try {
    $controller->dispatch($action);
} catch (NotFoundException $e) {
    $controller->dispatch($page, 'notfound', ['page' => $page, 'message' => $e->getMessage()]);
} catch (ForbiddenException $e) {
    $controller->dispatch($page, 'forbidden', ['page' => $page, 'message' => $e->getMessage()]);
} catch (Exception $e) {
    $controller->dispatch($page, 'error', ['page' => $page, 'message' => $e->getMessage()]);
}
