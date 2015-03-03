<?php
namespace ukatama\Mew\Controller;

use ukatama\Mew\Config;
use ukatama\Mew\Error\InternalErrorException;
use ukatama\Mew\Input;
use ukatama\Mew\Page;

class MarkdownController extends Controller {
    public function view() {
        $page = new Page(Input::get('p', Config::get('index')), 'name');
        $name = urlencode($page->getName() . '.md');
        header('Content-Type: text/x-markdown; charset="UTF-8"');
        header("Content-Disposition: inline; filename=\"$name\"");
        echo $page->getHead()->getData();
        return false;
    }

    public function zip() {
        $zip = new \ZipArchive;

        $basename = 'mew-' . hash('sha256', (new \DateTime)->getTimestamp()) . '.zip';
        $filename = '/tmp/' . $basename;
        //$filename = 'php://memory/maxmemory:536870912';

        if ($zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new InternalErrorException;
        }

        foreach (Page::getPages() as $page) {
            $head = $page->getHead();
            if ($head) {
                $markdown = 'Title: ' . $page->getName() . "\r\n" . $head->getData();
                if (!$zip->addFromString(mb_convert_encoding($page->getName(), 'sjis-win', 'UTF-8') . '.md', $markdown)) {
                    throw new InternalErrorException;
                }
            }
        }

        $zip->close();

        $bin = file_get_contents($filename);
        $size = filesize($filename);

        $filename = basename($basename);

        header('Content-Type: application/x-compress');
        header("Content-Length: $size");
        header("Content-Disposition: inline; filename=\"$filename\"");

        echo $bin;

        unlink($filename);

        return false;
    }

    public function upload() {
        if (Input::hasFile('files') && $this->method === 'POST') {
            $files = Input::file('files');

            $num = count($files['name']);
            $saved = 0;
            for ($i = 0; $i < $num; ++$i) {
                $file = array_combine(array_keys($files), array_map(function ($value) use ($i) {
                    return $value[$i];
                }, array_values($files)));

                if (!is_uploaded_file($file['tmp_name'])) {
                    throw new InternalErrorException;
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

                if ($ext == 'md') {
                    $markdown = file_get_contents($file['tmp_name']);

                    $page_name = basename($file['name'], '.md');
                    $markdown = preg_replace_callback('/Title: (.*?)(\r\n|\r|\n)/', function ($matches) use (&$page_name) {
                        $page_name = $matches[1];
                        return '';
                    }, $markdown);

                    $page = new Page($page_name, 'name');
                    $page->update($markdown);
                    ++$saved;
                }
            }

            if ($saved == 1) {
                return $this->redirect(['p' => $page_name]);
            }
        }
    }

}
