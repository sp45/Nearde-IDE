<?php
namespace app\modules;

use php\io\File;
use php\lib\fs;
use php\lang\Environment;
use php\compress\ZipFile;
use std, gui, framework, app;


class MainModule extends AbstractModule
{
    
    public function getProjects() : IdeProjects
    {
        $ideProjectsClass = new IdeProjects;
        $ideProjectsClass->registerType(new JphpGuiProjectType);
        return $ideProjectsClass;
    }
    
    public static function getOS()
    {
        $osName = System::getProperty('os.name');
        if ($osName == "linux")
            return $osName;
        else {
            $win = strtolower(explode(" ", $osName)[0]);
            if ($win == "windows")
                return $win;
            else 
                return "other";
        }
            
    }
    
    public static function makeEnv()
    {
        $env = System::getEnv();
        
        $env['ANT_HOME'] = fs::abs("./tools/ant");
        
        $env['Path'] .= ';' . $env['ANT_HOME'] . '\bin';
        
        return $env;
    } 
    
}
