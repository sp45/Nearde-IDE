<?php
namespace utils;

use app;

class IdeProjects 
{
    private $types;
    
    public function registerType(ProjectType $type)
    {
        if ($this->types[$type->getId()]) return;
        
        $this->types[$type->getId()] = $type;
    }
    
    public function getType($id)
    {
        if (!$this->types[$id]) return;
        
        return $this->types[$id];
    }
    
    public function getAllTypes()
    {
        return $this->types;
    }
}