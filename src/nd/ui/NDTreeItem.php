<?php
namespace nd\ui;

use gui;

class NDTreeItem extends UXTreeItem
{
    private $path;
    
    public function __construct(string $name, string $path)
    {
        parent::__construct($name);
        $this->path = $path;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
}