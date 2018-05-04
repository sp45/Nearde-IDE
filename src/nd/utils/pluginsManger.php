<?php
namespace nd\utils;

use framework;

class pluginsManger 
{
    private $plugins;
    
    public function registerPlugin(String $name, $class)
    {
        $name = strtoupper($name);
        if ($this->plugins[$name]) return;
        Logger::info("Register plugin: " . $name);
        $this->plugins[$name] = $class;
    }
    
    public function getPlugin($name)
    {
        $name = strtoupper($name);
        Logger::info("Getting plugin: " . $name);
        if ($this->plugins[$name])
            return $this->plugins[$name];
        else return null;
    }
    
    public function getAll()
    {
        Logger::info("Getting all plugins");
        return $this->plugins;
    }
}