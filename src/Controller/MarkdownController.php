<?php
namespace ukatama\Mew\Controller;

use ukatama\Mew\Config;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Input;
use ukatama\Mew\Page;

class MarkdownController extends Controller {
    public function view() {
        $page = new Page(Input::get('p', Config::get('index')));
        $name = urlencode($page->name . '.md');
        header('Content-Type: text/x-markdown; charset="UTF-8"');
        header("Content-Disposition: inline; filename=\"$name\"");
        echo $page->getMarkdown();
        return false;
    }

    public function zip() {
        $zip = new \ZipArchive;

        $filename = '/tmp/mew-' . hash('sha256', (new \DateTime)->getTimestamp()) . '.zip';

        if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
            throw new InternalErrorException;
        }

        foreach (glob(dirname(dirname(dirname(__FILE__))) . '/page/*.md') as $md) {
            $page = new Page(basename($md, '.md'), true);
            $page->getMarkdown();
            $zip->addFile($md, mb_convert_encoding($page->name, 'sjis-win', 'UTF-8') . '.md');
        }

        $zip->close();

        $bin = file_get_contents($filename);
        $size = count($bin);

        $filename = basename($filename);

        header('Content-Type: application/x-compress');
        header("Content-Length: $size");
        header("Content-Disposition: inline; filename=\"$filename\"");

        echo $bin;

        unlink($filename);

        return false;
    }
}
