<?php

namespace plugins\jphp;
use gui;
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
        
        $format->registerFileTemplate(NDTreeContextMenu::createItem("PHP файл.", $format->getIcon("php"), function ($item) {
            FileUtils::createFile($item->userData, UXDialog::input("Ввидите название нового php файла.") . ".php", "<?php \n");
        }));
        
        $format->registerFileTemplate(NDTreeContextMenu::createItem("PHP класс.", $format->getIcon("php"), function ($item) {
            $name = UXDialog::input("Ввидите название нового php класса.");
            FileUtils::createFile($item->userData, $name . ".php", 
            "<?php \n\nclass $name { \n\n}"
            );
        }));
        
        // include external classes
        include fs::abs("./plugins/jphp/classes/JPHPConsoleProjectTemplate.php");
        
        IDE::get()->getProjectManger()->registerTemplate("JPHP temp 1", new \plugins\jphp\classes\JPHPConsoleProjectTemplate());
    }
}

// делаем так чтобы nearde могла увидеть плагин
IDE::get()->getPluginsManger()->registerPlugin("jphp", new jphpPlugin());