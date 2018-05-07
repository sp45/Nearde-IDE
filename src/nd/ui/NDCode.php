<?php
namespace nd\ui;

use std;
use nd;
use gui;
use \php\gui\UXCode;

class NDCode extends UXCode
{
    
    public function __construct($file, $lang = "php", $readOnly = false)
    {
        parent::__construct(function () use ($file, $readOnly) {
            // on editor init
            if (is_string($file))
                $this->text = $file;
            if ($file instanceof File)
            {
                if (fs::isDir($file))
                    $this->text = Stream::getContents($file);   
            }
            $this->setTheme(IDE::get()->getConfig()['settings']['editorStyle']);
            $this->setShowInvisibles(IDE::get()->getConfig()['settings']['invisibles']);
            $this->setFontSize(IDE::get()->getConfig()['settings']['font_size']);
            $this->setReadOnly($readOnly);
        }, $lang);
        $this->userData = $this;
        $this->on('keyUp', function () use ($file) {
            $this->save($file);
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
        if (fs::exists($path))
            Stream::putContents($path, $this->text);
    }
    
}