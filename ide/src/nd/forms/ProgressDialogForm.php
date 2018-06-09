<?php
namespace nd\forms;

use bundle\http\HttpDownloader;
use compress\ZipArchive;
use compress\ZipArchiveEntry;
use Exception;
use php\io\File;
use php\io\Stream;
use php\lib\fs;
use php\lang\Thread;
use php\lib\arr;
use nd\modules\IDE;
use std, gui, framework, nd;


class ProgressDialogForm extends AbstarctIDEForm
{
    private $res = false;
    
    public function unpack(string $zip, string $dir)
    {
        try {
            $zip = new ZipArchive($zip);
            $zip->readAll(function (ZipArchiveEntry $entry, ?Stream $stream) use ($dir) {
                $file = fs::abs($dir . '/' . $entry->name);
                echo 'Unpack -> ' . $file . "\n";
                if (!$entry->isDirectory())
                {
                    fs::makeDir(fs::parent($file));
                    fs::copy($stream, $file);
                }
                else fs::makeDir($file);
            });
            $this->res = true;
        } catch (\Error $exception)
        {
            $this->res = false;
        }

        return $this->res;
    }

    public function download(string $url, string $to)
    {
        $this->title = "Загрузка";

        $this->label->text = $url;

        $dwn = new HttpDownloader();
        $dwn->urls = [$url];
        $dwn->destDirectory = $to;

        $dwn->on('successAll', function () {
            $this->res = true;
            $this->hide();
        });

        $dwn->on('errorOne', function () {
            IDE::dialog("Не удалось скачать файл.");
            $this->hide();
        });

        $dwn->start();

        $this->showAndWait();
        
        return $this->res;
    }
}