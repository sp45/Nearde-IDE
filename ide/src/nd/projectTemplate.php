<?php
namespace nd;

use gui;
use facade\Json;
use std;
use nd;

use php\lib\fs;
use nd\modules\IDE;
use php\gui\UXImageView;

abstract class projectTemplate
{
    abstract public function getName();
    abstract public function getIcon();
    abstract public function getDscription();
    
    private $commands;
    private $gunters;
    
    public function makeProject($project)
    {
        $file = fs::abs($project->getPath() . "/" .  $project->getName() . ".ndproject");
        if (fs::makeFile($file)){
            fs::makeDir($project->getPath() . "/.nd");
            Json::toFile($file, [
                "name" => $project->getName(),
                "template" => get_class($project->getTemplate())
            ]);
            $project->loadConfig($file);
            return true;
        } else {
            IDE::dialog("Не удалось создать проект.");
            return false;
        }
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
    
    public function regiserGunter(string $name, UXImageView $img, callable $callable, string $text = null)
    {
        if ($this->gunters[$name]) return;
        
        $this->gunters[$name] = [
            'name'     => $name,
            'image'    => $img,
            'callable' => $callable,
            'text'     => $text
        ];
    }
    
    public function getGunters()
    {
        return $this->gunters;
    }
}