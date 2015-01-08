<?php

namespace ukatama\Mew;

class Config {
    protected static $_loaded = false;
    protected static $_config = [];

    protected static function _load() {
        include(dirname(dirname(__FILE__)) . '/config/wiki.php');
        Config::$_config = $config;
        Config::$_loaded = true;
    }

    public static function get($name) {
        if (!Config::$_loaded) {
            Config::_load();
        }
        return array_key_exists($name, Config::$_config) ? Config::$_config[$name] : null;
    }

    public static function set($name, $value) {
        Config::$_config[$name] = $value;
    }

    public static function backup() {
        if (!Config::$_loaded) {
            Config::_load();
        }
        return Config::$_config;
    }

    public static function restore($data) {
        Config::$_config = $data;
    }
}
