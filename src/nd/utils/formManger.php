<?php
namespace nd\utils;

class formManger 
{
    private $forms;
    
    public function registerForm(String $name, $class)
    {
        if ($this->forms[$name]) return;
        $this->forms[$name] = $class;
    }
    
    public function getForm($name)
    {
        return new $this->forms[$name];
    }
}