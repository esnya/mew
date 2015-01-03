<?php
namespace ukatama\Mew;

class Utility {
    static public function CamelCase($str) {
        return preg_replace_callback('/ +([^ ])/', function ($matches) {
            return strtoupper($matches[1]);
        }, preg_replace_callback('/^[a-z]/', function ($matches) {
            return strtoupper($matches[0]);
        }, $str));
    }
}
