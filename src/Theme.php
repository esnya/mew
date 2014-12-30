<?php
namespace ukatama\Mew;

class Theme {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    protected function _parse($action, $code, $variables) {
        $extended = $variables;

        $code = preg_replace_callback('/@block\((.*?)\)((.|\r|\n)*?)@endblock/m', function ($matches) use (&$extended, $variables, $action) {
            $block = $matches[1];
            $code = $matches[2];
            $extended[$block] = $this->_parse($action, $code, $variables);
            return '';
        }, $code);

        if (preg_match('/@extends\((.*?)\)/', $code, $matches)) {
            return $this->_renderer($matches[1], $extended);
        } else {
            $code = preg_replace_callback('/@foreach\((.*) +in +(.*)\)((.|\r|\n)*?)@endforeach/m', function ($matches) use($action, $variables) {
                $name = $matches[1];
                $array = $matches[2];
                $code = $matches[3];

                if (!array_key_exists($array, $variables) || !is_array($variables[$array])) {
                    return '';
                }

                return implode(array_map(function ($value) use ($action, $variables, $code, $name) {
                    $variables[$name] = $value;
                    return $this->_parse($action, $code, $variables);
                }, $variables[$array]));
            }, $code);

            $code = preg_replace_callback('/{{(.*?)}}/', function ($matches) use($variables) {
                $path = explode('.', $matches[1]);
                $scope = $variables;

                foreach ($path as $name) {
                    if (!array_key_exists($name, $scope)) {
                        return '';
                    }
                    $scope = $scope[$name];
                }

                return $scope;
            }, $code);
        }

        return $code;
    }

    protected function _renderer($action, $variables) {
        $path = dirname(dirname(__FILE__)) . '/theme/' . $this->name . '/' . $action . '.php';
        $code = file_get_contents($path);

        $variables['theme'] = $this->name;

        return $this->_parse($action, $code, $variables);
    }

    public function render($action, $variables = []) {
        echo $this->_renderer($action, $variables);
    }
}
