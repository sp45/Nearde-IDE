<?php
namespace utils;

use app;
use facade\Json;
use \php\lang\Process;

class Project 
{
    private $dir;
    private $name;
    private $type;
    private $jsonConfig;
    
    public function Open($path, $name)
    {
        $json = Json::fromFile($path . "/" . $name . ".nrd");
        if ($json == []) return;
        
        $type = MainModule::getProjects()->getType($json['type']);
        if (!$type) return;
        
        $this->dir  = $path;
        $this->name = $name;
        $this->type = $type;
        $this->jsonConfig = $json;
        
        app()->getForm("project")->OpenProject($this);
        return true;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getDir()
    {
        return $this->dir;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getJson()
    {
        return $this->jsonConfig;
    }
}
