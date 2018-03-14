<?php
namespace platforms\JphpGuiPlatform\project;

use platforms\JphpGuiPlatform\JphpGuiBuildType;
use php\lang\Thread;
use php\io\Stream;
use php\io\File;
use php\lib\fs;
use app\modules\MainModule;
use bundle\zip\ZipFileScriptStopException;
use std;
use app;
use \php\lang\Process;
use Types\ProjectType;

class JphpGuiProjectType extends ProjectType
{
    
    public function getName()
    {
        return "JPHP GUI прилолжение";
    }
    
    public function getId()
    {
        return __CLASS__;
    }
    
    public function getIcon()
    {
        return "res://.data/img/windows32.png";
    }
    
    public function getSdk()
    {
        return "JphpGuiProjectSdk.zip";
    }
    
    public function getDescription(){
        return "Программа на JPHP с JavaFX GUI";
    }
}