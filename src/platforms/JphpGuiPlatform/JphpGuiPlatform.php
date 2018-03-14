<?php

namespace platforms\JphpGuiPlatform;

use php\framework\Logger;
use platforms\JphpGuiPlatform\project\JphpGuiProjectType;
use platforms\JphpGuiPlatform\run\JphpGuiRunType;
use platforms\JphpGuiPlatform\build\JphpGuiBuildOneJarType;
use utils\AbstractPlatform;

class JphpGuiPlatform extends AbstractPlatform
{
    public function onRegister()
    {
        $this->registerProjectType(new JphpGuiProjectType()); // project type
        $this->registerRunType(new JphpGuiRunType()); // runing jphp
        $this->registerBuildType(new JphpGuiBuildOneJarType()); // onejar build type
    }
    
    public function getId()
    {
        return __CLASS__;
    }
}