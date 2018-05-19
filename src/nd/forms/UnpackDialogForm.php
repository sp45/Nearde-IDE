<?php
namespace nd\forms;

use Exception;
use php\compress\ZipFile;
use std, gui, framework, nd;


class UnpackDialogForm extends AbstractForm
{
    private $res = false;
    
    public function unpack(string $zip, string $dir, bool $dialog = true)
    {
        $zip = new ZipFile($zip);
        new Thread(function () use ($zip, $dir, $dialog) {
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
        })->start();
        $this->showAndWait();
        return $this->res;
    }
}