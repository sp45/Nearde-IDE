<?php
namespace nd\utils;

use framework;

class projectManger 
{
    private $templates;
    
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
}