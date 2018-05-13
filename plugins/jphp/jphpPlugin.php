<?php

namespace plugins\jphp;
use gui;
use std;
use nd;

class jphpPlugin extends Plugin
{
    public function getName()
    {
        return "PHP";
    }
    
    public function getIcon()
    {
        return "./plugins/jphp/data/images/php.png";
    }
    
    public function getDscription()
    {
        return "Работа с файлами php.";
    }
    
    public function getAuthor()
    {
        return "MWStudio";
    }
    
    public function onIDEStarting()
    {
        /** @var fileFormat $format */
        $format = IDE::get()->getFileFormat();
        $format->registerIcon("php", "./plugins/jphp/data/images/phpIcon.png");
        
        $format->registerFileTemplate(NDTreeContextMenu::createItem("PHP файл.", $format->getIcon("php"), function ($item) {
            FileUtils::createFile($item->userData, IDE::inputDialog("Ввидите название нового php файла.") . ".php", "<?php \n");
        }));
        
        $format->registerFileTemplate(NDTreeContextMenu::createItem("PHP класс.", $format->getIcon("php"), function ($item) {
            $name = IDE::inputDialog("Ввидите название нового php класса.");
            FileUtils::createFile($item->userData, $name . ".php", 
            "<?php \n\nclass $name { \n\n}"
            );
        }));
    }
}