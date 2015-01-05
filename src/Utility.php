<?php
namespace ukatama\Mew;

class Utility {
    static public function CamelCase($str) {
        return preg_replace_callback('/_+([^_])/', function ($matches) {
            return strtoupper($matches[1]);
        }, preg_replace_callback('/^[a-z]/', function ($matches) {
            return strtoupper($matches[0]);
        }, $str));
    }
}
