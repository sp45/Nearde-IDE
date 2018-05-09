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
    
    private $commands;
    
    public function makeProject($project)
    {
        $file = fs::abs($project->getPath() . "/" .  $project->getName() . ".ndproject");
        fs::makeFile($file);
        fs::makeDir($project->getPath() . "/.nd");
        Json::toFile($file, [
            "name" => $project->getName(),
            "template" => get_class($project->getTemplate())
        ]);
        $project->loadConfig($file);
    }
    
    public function registerCommand(string $type, callable $func)
    {
        if ($this->commands[$type]) return;
        $this->commands[$type] = $func;
    }
    
    public function getCommand(string $type)
    {
        return $this->commands[$type];
    }
}