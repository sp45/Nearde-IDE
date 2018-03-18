<?php

namespace platforms\JphpConsolePlatform;

use utils\AbstractPlatform;

class JphpConsolePlatform extends AbstractPlatform
{
    public function onRegister()
    {
        $this->registerProjectType(new project\JphpConsoleProjectType()); // project type
        $this->registerRunType(new run\JphpConsoleRunType()); // runing jphp
        
        // builds 
        $this->registerBuildType(new build\JphpConsoleBuildOneJarType()); // onejar
    }
    
    public function getId()
    {
        return __CLASS__;
    }
}