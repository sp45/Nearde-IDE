<?php

namespace nd\plugins\jphp;
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
        return "res://nd/plugins/jphp/data/images/php.png";
    }
    
    public function getDscription()
    {
        return "Написание приложений на jphp.";
    }
    
    public function onIDEStarting()
    {
        /** @var fileFormat $format */
        $format = IDE::get()->getFileFormat();
        $format->registerIcon("php", IDE::image("res://nd/plugins/jphp/data/images/phpIcon.png"));
    }
}