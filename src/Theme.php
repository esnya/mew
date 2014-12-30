<?php
namespace ukatama\Wiki;

class Theme {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    protected function _parse($code, $variables) {
        $code = preg_replace_callback('/@foreach\((.*) +in +(.*)\)((.|\r|\n)*?)@endforeach/m', function ($matches) use($variables) {
            $name = $matches[1];
            $array = $matches[2];
            $code = $matches[3];

            if (!array_key_exists($array, $variables) || !is_array($variables[$array])) {
                return '';
            }

            return implode(array_map(function ($value) use ($variables, $code, $name) {
                $variables[$name] = $value;
                return $this->_parse($code, $variables);
            }, $variables[$array]));
        }, $code);

        $code = preg_replace_callback('/{{(.*?)}}/', function ($matches) use($variables) {
            $path = explode('.', $matches[1]);
            $scope = $variables;

            foreach ($path as $name) {
                if (!array_key_exists($name, $scope)) {
                    return $matches[0];
                }
                $scope = $scope[$name];
            }

            return $scope;
        }, $code);

        return $code;
    }

    public function render($action, $variables = []) {
        $path = dirname(dirname(__FILE__)) . '/theme/' . $this->name . '/' . $action . '.php';
        $code = file_get_contents($path);

        $variables['theme'] = $this->name;

        echo $this->_parse($code, $variables);
    }
}
