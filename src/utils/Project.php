<?php
namespace utils;

use php\framework\Logger;
use app;
use facade\Json;
use \php\lang\Process;

class Project 
{
    private $dir;
    private $name;
    private $type;
    private $platform;
    private $jsonConfig;
    
    public function Open($path, $name)
    {
        Logger::info("Check file");
        var_dump($path . "/" . $name . ".nrd");
        $json = Json::fromFile($path . "/" . $name . ".nrd");
        if ($json == []) return;
        Logger::info("Check file - OK");
        
        Logger::info("Check platform");
        $platform = MainModule::getProjects()->getPlatform($json['platform']);
        if (!$platform) return;
        Logger::info("Check platform - OK");
        
        Logger::info("Check ProjectType");
        $type = $platform->getProjectType();
        if (!$type) return;
        Logger::info("Check ProjectType - OK");
        
        $this->dir  = $path;
        $this->name = $name;
        $this->type = $type;
        $this->platform = $platform;
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
    
    public function getPlatform()
    {
        return $this->platform;
    }
    
    public function build()
    {
        $form = app()->getForm("BuildType");
        $form->show();
        
        foreach ($this->platform->getAllBuildTypes() as $type)
        {
            $form->addItem($type, $this);
        }
    }
}
