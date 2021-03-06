<?php
namespace nd\ui;

use php\gui\event\UXKeyEvent;
use php\io\IOException;
use std;
use nd;
use gui;
use \php\gui\UXCode;
use php\io\Stream;
use nd\modules\IDE;
use php\lib\fs;

class NDCode extends UXCode
{
    /**
     * @var callable
     */
    public $onSave;

    public function __construct($file, $lang = "text", $readOnly = false)
    {
        parent::__construct(function () use ($file, $readOnly) {
            // on editor init
            if (is_string($file))
                $this->text = $file;
            if (fs::exists($file))
                $this->text = Stream::getContents($file);   

            $this->setTheme(IDE::get()->getConfig()['settings']['editor']['style']);
            $this->setShowInvisibles(IDE::get()->getConfig()['settings']['editor']['invisible']);
            $this->setFontSize(IDE::get()->getConfig()['settings']['editor']['font_size']);
            $this->setReadOnly($readOnly);
        }, $lang);
        $this->userData = $this;

        $this->on('keyUp', function (UXKeyEvent $event) use ($file) {
            if ($event->codeName == 'Enter')
                $this->engine->executeScript('editor.insert(\'\n\')'); // Костыль от java 10 -_-
            // Не ну это полная жопа. Разрабы java Что вы курили а?

            $this->save($file);
            call_user_func($this->onSave, $file);
        });
    }
    
    public function setTheme($name)
    {
        $this->engine->executeScript("editor.setTheme('ace/theme/$name')");
    }
    
    public function setReadOnly(bool $read)
    {
        $this->engine->executeScript("editor.setReadOnly($read)");
    }
    
    public function setShowInvisibles(bool $invisible)
    {
        $this->engine->executeScript("editor.setShowInvisibles($invisible)");
    }
    
    public function setFontSize(int $size)
    {
        $this->engine->executeScript("editor.setFontSize($size)");
    }
    
    public function save($path)
    {
        if (!fs::exists($path)) return;

        try {
            Stream::putContents($path, $this->text);
        } catch (IOException $e) {
            ;
        }
    }
    
}