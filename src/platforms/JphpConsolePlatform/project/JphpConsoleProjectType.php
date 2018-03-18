<?php
namespace platforms\JphpConsolePlatform\project;

use Types\ProjectType;

class JphpConsoleProjectType extends ProjectType
{
    
    public function getName()
    {
        return "JPHP консольное прилолжение";
    }
    
    public function getId()
    {
        return __CLASS__;
    }
    
    public function getIcon()
    {
        return "res://.data/img/console-app.png";
    }
    
    public function getSdk()
    {
        return "JphpConsoleProjectSdk.zip";
    }
    
    public function getDescription(){
        return "Консольная программа на JPHP";
    }
}