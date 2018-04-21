<?php
namespace nd\utils;

use framework;

class pluginsManger 
{
    private $plugins;
    
    public function registerPlugin(String $name, $class)
    {
        if ($this->plugins[$name]) return;
        Logger::info("Register plugin: " . $name);
        $this->plugins[$name] = $class;
    }
    
    public function getPlugin($name)
    {
        Logger::info("Getting plugin: " . $name);
        return new $this->plugins[$name];
    }
    
    public function getAll()
    {
        Logger::info("Getting all plugins");
        return $this->plugins;
    }
}