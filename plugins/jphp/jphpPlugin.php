<?php

namespace plugins\jphp;
use std;
use nd;


class jphpPlugin extends Plugin
{
    public function getName()
    {
        return "JPHP";
    }
    
    public function getIcon()
    {
        return "./plugins/jphp/data/images/php.png";
    }
    
    public function getDscription()
    {
        return "Написание приложений на jphp.";
    }
    
    public function onIDEStarting()
    {
        /** @var fileFormat $format */
        $format = IDE::get()->getFileFormat();
        $format->registerIcon("php", "./plugins/jphp/data/images/phpIcon.png");
    }
}

// делаем так чтобы nearde могла увидеть плагин
IDE::get()->getPluginsManger()->registerPlugin("jphp", new jphpPlugin());