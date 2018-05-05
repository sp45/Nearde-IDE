<?php
namespace nd\ui;

use nd;
use gui;
use \php\gui\UXCode;

class NDCode extends UXCode
{
    
    public function __construct($text, $lang = "php")
    {
        parent::__construct(function () use ($text) {
            // on editor init
            $this->text = $text;
            $this->setTheme(IDE::get()->getConfig()['settings']['editorStyle']);
        }, $lang);
    }
    
    public function setTheme($name)
    {
        $this->engine->executeScript("editor.setTheme('ace/theme/$name')");
    }
    
}