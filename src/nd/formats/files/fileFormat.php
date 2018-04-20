<?php
namespace nd\formats\files;

use gui;
use nd;

class fileFormat 
{

    private $formats;
    
    public function init()
    {
        $this->formats = [
            'css' => IDE::ico('document.png')
        ];
    }
    
    public function getIcon(string $ext)
    {
        if (!$this->formats[$ext]) return IDE::ico("file.png");
        return $this->formats[$ext];
    }
    
    public function registerIcon(string $ext, UXImageView $ico)
    {
        if ($this->formats[$ext]) return;
        
        $this->formats[$ext] = $ico;
    }
    
}