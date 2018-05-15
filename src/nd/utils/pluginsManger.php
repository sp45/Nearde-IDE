<?php
namespace nd\utils;

use Exception;
use framework;

class pluginsManger 
{
    private $plugins;
    private $offline;
    
    public function registerPlugin(String $name, $class)
    {
        $name = strtoupper($name);
        if ($this->plugins[$name]) throw new Exception("Plugin exists.");
        $this->plugins[$name] = $class;
        $this->setOfflineToPlugin($name, false);
    }
    
    public function setOfflineToPlugin(string $pluginName, bool $offline)
    {
        $pluginName = strtoupper($pluginName); 
        if (!$this->plugins[$pluginName]) throw new Exception("Plugin not found.");
        
        $this->offline[$pluginName] = $offline;
    }
    
    public function getPlugin($name)
    {
        $name = strtoupper($name);
        if (!$this->plugins[$name]) throw new Exception("Plugin not found.");
        
        return $this->plugins[$name];
    }
    
    public function getOfflineForPlugin($name)
    {
        $pluginName = strtoupper($name);
        if (!$this->plugins[$pluginName]) throw new Exception("Plugin not found.");
        
        return $this->offline[$name];
    }
    
    public function getAll()
    {
        return $this->plugins;
    }
    
    public function getOfflineForAll()
    {
        return $this->offline;
    }
}