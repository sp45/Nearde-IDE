<?php
namespace utils;

use php\framework\Logger;
use utils\AbstractPlatform;
use app;

class IdeProjects 
{
    private $platforms;
    
    public function registerPlatform(AbstractPlatform $platform)
    {
        if ($this->platforms[$platform->getId()]) return;
        
        $this->platforms[$platform->getId()] = $platform;
    }
    
    public function getPlatform($id)
    {
        foreach ($this->platforms as $debug => $val) Logger::info($debug);
        if (!$this->platforms[$id]) return;
        
        return $this->platforms[$id];
    }
    
    public function getAllPlatforms()
    {
        return $this->platforms;
    }
}