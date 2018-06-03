<?php
namespace nd\utils;

use Exception;
use framework;

class pluginsManger 
{
    private $plugins;
    private $offline;

    /**
     * @param String $name
     * @param $class
     * @throws Exception
     */
    public function registerPlugin(String $name, $class)
    {
        $name = strtoupper($name);
        if ($this->plugins[$name]) throw new Exception("Plugin exists.");
        $this->plugins[$name] = $class;
        try {
            $this->setOfflineToPlugin($name, false);
        } catch (Exception $e) {
            ;
        }
    }

    /**
     * @param string $pluginName
     * @param bool $offline
     * @throws Exception
     */
    public function setOfflineToPlugin(string $pluginName, bool $offline)
    {
        $pluginName = strtoupper($pluginName); 
        if (!$this->plugins[$pluginName]) throw new Exception("Plugin not found.");
        
        $this->offline[$pluginName] = $offline;
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function getPlugin($name)
    {
        $name = strtoupper($name);
        if (!$this->plugins[$name]) throw new Exception("Plugin not found.");
        
        return $this->plugins[$name];
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function getOfflineForPlugin($name)
    {
        $pluginName = strtoupper($name);
        if (!$this->plugins[$pluginName]) throw new Exception("Plugin not found.");
        
        return $this->offline[$name];
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->plugins;
    }

    /**
     * @return mixed
     */
    public function getOfflineForAll()
    {
        return $this->offline;
    }
}