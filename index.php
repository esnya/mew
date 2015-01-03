<?php
require_once __DIR__ . '/vendor/autoload.php';

use ukatama\Mew\Config;
use ukatama\Mew\Input;
use ukatama\Mew\PageController;
use ukatama\Mew\Registry;

ini_set('display_errors', Config::get('debug'));

try {
    Registry::controller(Input::get('c', 'page'))->dispatch(Input::get('a', 'view'));
} catch (Exception $e) {
    $ctr = Registry::controller('error');
    $ctr->error = $e;
    $ctr->dispatch('error');
}
