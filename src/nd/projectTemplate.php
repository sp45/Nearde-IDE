<?php
namespace nd;

use facade\Json;
use std;
use nd;

abstract class ProjectTemplate 
{
    abstract public function getName();
    abstract public function getIcon();
    abstract public function getDscription();
    
    public function makeProject($project)
    {
        $path = FileUtils::createFile($project->getPath(), $project->getName() . ".ndproject", Json::encode([
            "name" => $project->getName(),
            "template" => $project->getTemplate()
        ]));
        $project->loadConfig($path);
    }
}