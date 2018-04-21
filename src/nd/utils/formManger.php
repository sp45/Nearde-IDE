<?php
namespace nd\utils;

use framework;

class formManger 
{
    private $forms;
    
    public function registerForm(String $name, $class)
    {
        if ($this->forms[$name]) return;
        Logger::info("Register form: " . $name);
        $this->forms[$name] = $class;
    }
    
    public function getForm($name)
    {
        Logger::info("Getting form: " . $name);
        return new $this->forms[$name];
    }
}