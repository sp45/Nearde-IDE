<?php
namespace nd\ui;

use std;
use nd;
use gui;
use \php\gui\UXCode;

class NDCode extends UXCode
{
    
    public function __construct($text, $lang = "php", $readOnly = false)
    {
        parent::__construct(function () use ($text, $readOnly) {
            // on editor init
            $this->text = $text;
            $this->setTheme(IDE::get()->getConfig()['settings']['editorStyle']);
            $this->setReadOnly($readOnly);
        }, $lang);
        $this->userData = $this;
    }
    
    public function setTheme($name)
    {
        $this->engine->executeScript("editor.setTheme('ace/theme/$name')");
    }
    
    public function setReadOnly(bool $read)
    {
        $this->engine->executeScript("editor.setReadOnly($read)");
    }
    
    public function save($path)
    {
        Stream::putContents($path, $this->text);
    }
    
}