<?php

namespace ukatama\Mew\Converter;

use Michelf\MarkdownExtra;
use ukatama\Mew\Page;

class MarkdownConverter extends Converter {
    private $pageStuck = [];

    public function convert($src, $page = '') {
        $this->pageStuck[$page] = true;

        $src = preg_replace_callback('/((.|\r|\n)*?)((```(.|\r|\n)*?```)|(`(.|\r|\n)*?`)|$)/m', function ($matches) use ($page) {
            $src = $matches[1];

            // File Transclusion
            $src = preg_replace_callback('/{{(.*?)}}/', function($matches) use ($page) {
                $name = $matches[1];
                if (!array_key_exists($name, $this->pageStuck)) {
                    $page = new Page($name, 'name');
                    return $this->convert($page->getHead()->getData(), $name);
                } else {
                    return $matches[0];
                }
            }, $src);

            // Abbr link
            $src = preg_replace('/\[(.*?)\](?!\()/', '[$1]($1)', $src);
            $src = preg_replace_callback('/(!?)\[(.*?)]\((.*?)( .*?)?\)/', function($matches) use ($page) {
                if (preg_match('/^(https?)?:\/\//', $matches[3])) {
                    return $matches[0];
                } else {
                    if (empty($matches[1])) {
                        $encoded = urlencode($matches[3]);
                        if (count($matches) == 5) {
                            return "[{$matches[2]}](?p=$encoded {$matches[4]})";
                        } else {
                            return "[{$matches[2]}](?p=$encoded)";
                        }
                    } else {
                        preg_match('/^((.*)\/)?([^\/]+)$/', $matches[3], $path);

                        if (strlen($path[2]) == 0) {
                            $page_name = urlencode($page);
                        } else {
                            $page_name = urlencode($path[2]);
                        }

                        $file_name = urlencode($path[3]);
                        if (count($matches) == 5) {
                            return "![{$matches[2]}](?a=file&p=$page_name&f=$file_name {$matches[4]})";
                        } else {
                            return "![{$matches[2]}](?a=file&p=$page_name&f=$file_name)";
                        }
                    }
                }
            }, $src);

            return $src . $matches[3];
        }, $src);
        return MarkdownExtra::defaultTransform($src);
    }
}
