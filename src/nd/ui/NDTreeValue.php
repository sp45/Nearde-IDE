<?php
namespace nd\ui;

class NDTreeValue 
{
    public $path;
    public $text;
    
    public function __construct($text, $path)
    {
        $this->path = $path;
        $this->text = $text;
    }
    
    public function __toString()
    {
        return $this->text;
    }
}