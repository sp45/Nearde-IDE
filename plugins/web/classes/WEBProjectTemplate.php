<?php
namespace plugins\web\classes;

use std;
use std;
use nd;

class WEBProjectTemplate extends ProjectTemplate
{
    public function __construct()
    {
        $this->registerCommand("run", function ($path) {
            if (fs::exists(fs::abs($path . "/index.html")))
            {
                open(fs::abs($path . "/index.html"));
            }
            
            return null;
        });
    }
    
    public function getName()
    {
        return "Web сайт.";
    }
    
    public function getIcon()
    {
        return "./plugins/web/images/www.png";
    }
    
    public function getDscription()
    {
        return "Написание web сайтов на html / css / js.";
    }
    
    public function makeProject($project)
    {
        if (!parent::makeProject($project)) return false;
        $path = $project->getPath();
        $name = $project->getName();
        fs::makeDir(fs::abs($path . "/src"));
        fs::makeFile(fs::abs($path . "/src/styles.css"));
        Stream::putContents(fs::abs($path . "/index.html"), str_replace("%NAME%", $name, Stream::getContents("./plugins/web/data/template.html")));
        return true;
    }
}