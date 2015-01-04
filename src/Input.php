<?php
namespace ukatama\Mew;

class Input {
    static function has($name) {
        return array_key_exists($name, $_REQUEST);
    }

    static function get($name, $default = NULL) {
         return Input::has($name) ? $_REQUEST[$name] : $default;
    }

    static function all() {
        return $_REQUEST;
    }

    static function hasFile($file) {
        return array_key_exists($file, $_FILES);
    }

    static function file($file) {
        return Input::hasFile($file) ? $_FILES[$file] : null;
    }
}
