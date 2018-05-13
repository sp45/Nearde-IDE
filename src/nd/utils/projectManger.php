<?php
namespace nd\utils;

use gui;
use framework;

class projectManger 
{
    private $templates;
    
    private $gunters;
    
    public function registerTemplate(String $name, $class)
    {
        if ($this->templates[$name]) return;
        Logger::info("Register template: " . $name);
        $this->templates[$name] = $class;
    }
    
    public function getTemplate($name)
    {
        Logger::info("Getting template: " . $name);
        return $this->templates[$name];
    }
    
    public function getAll()
    {
        Logger::info("Getting all templates.");
        return $this->templates;
    }
    
    public function regiserGlobalGunter(string $name, UXImageView $img, callable $callable, $text = null)
    {
        if ($this->gunters[$name]) return;
        
        $this->gunters[$name] = [
            'name'     => $name,
            'image'    => $img,
            'callable' => $callable,
            'text'     => $text
        ];
    }
    
    public function getGlobalGunters()
    {
        return $this->gunters;
    }
}