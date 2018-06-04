<?php
namespace nd\forms;

use bundle\http\HttpDownloader;
use Exception;
use php\compress\ZipFile;
use php\lang\Thread;
use php\lib\arr;
use nd\modules\IDE;
use std, gui, framework, nd;


class ProgressDialogForm extends AbstarctIDEForm
{
    private $res = false;
    
    public function unpack(string $zip, string $dir, bool $dialog = true)
    {
        $zip = new ZipFile($zip);
        $t = new Thread(function () use ($zip, $dir, $dialog) {
            try {
                $unpacked = 0;
                $count = arr::count($zip->statAll());
                $zip->unpack($dir, null, function ($name) use (&$unpacked, $count, $dialog) {
                    $unpacked += 1;
                    $isEnd = $unpacked == $count; 
                    uiLater(function()use($unpacked, $isEnd, $count, $name, $dialog){
                        $this->progressBar->progressK = $unpacked / $count;
                        if($isEnd)
                        {
                            $this->res = true;
                            if ($dialog)
                                IDE::dialog("Распаковка завершина.");
                            
                            $this->hide();
                        }
                        $this->label->text = $name.' ( '.$unpacked.' / '.$count.' )';
                    });
                });
            } catch(Exception $e)
            {
                uiLater(function () {
                    IDE::dialog('Ошибка при распаковке архива!');
                    $this->hide();
                });
            }
        });
        $t->start();
        $this->showAndWait();
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