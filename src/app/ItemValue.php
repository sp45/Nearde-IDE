<?php
namespace app;

class ItemValue 
{
    public $id;
    
    public $text;
    
    public function __construct($id, $text)
    {
        $this->id = $id;
        $this->text = $text;
    }
    
    public function __toString()
    {
        return $this->text;
    }
}