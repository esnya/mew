<?php
namespace ukatama\Wiki;

class Theme {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function render($action, $variables = []) {
        $path = dirname(dirname(__FILE__)) . '/theme/' . $this->name . '/' . $action . '.php';
        $code = file_get_contents($path);

        $variables['theme'] = $this->name;

        echo preg_replace(
            '/{{.*?}}/',
            '',
            str_replace(
                array_map(function ($name) { return '{{'.$name.'}}'; },
                array_keys($variables)),
                array_values($variables),
                $code)
            );
    }
}
