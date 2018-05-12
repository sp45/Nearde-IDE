<?
namespace plugins\web;

use gui;
use nd;


class webPlugin extends Plugin
{
    public function getName()
    {
        return "web";
    }
    
    public function getIcon()
    {
        return "./plugins/web/images/www.png";
    }
    
    public function getDscription()
    {
        return "Добовляет возможность писать web сайты на html / css / js";
    }
    
    public function getAuthor()
    {
        return "MWStudio";
    }
    
    public function onIDEStarting()
    {
        
        echo '                                              ' . "\n";
        echo '             __      ___            _  _____  ' . "\n";
        echo ' _    _____ / /     / _/__  ____   / |/ / _ \ ' . "\n";
        echo '| |/|/ / -_) _ \   / _/ _ \/ __/  /    / // / ' . "\n";
        echo '|__,__/\__/_.__/  /_/ \___/_/    /_/|_/____/  ' . "\n";
        echo '                                              ' . "\n";
        echo '                                              ' . "\n";
        
        // :3
        
        /** @var fileFormat $format */
        $format = IDE::get()->getFileFormat();
        $format->registerIcon("html", "./plugins/web/images/html.png");
        $format->registerIcon("js", "./plugins/web/images/nodejs.png");
        
        $format->registerFileTemplate(NDTreeContextMenu::createItem("HTML файл.", $format->getIcon("html"), function ($item) {
            FileUtils::createFile($item->userData, UXDialog::input("Ввидите название нового html файла.") . ".html");
        }));
        
        $format->registerFileTemplate(NDTreeContextMenu::createItem("CSS файл.", $format->getIcon("css"), function ($item) {
            FileUtils::createFile($item->userData, UXDialog::input("Ввидите название нового css файла.") . ".css");
        }));
        
        $format->registerFileTemplate(NDTreeContextMenu::createItem("JS файл.", $format->getIcon("js"), function ($item) {
            FileUtils::createFile($item->userData, UXDialog::input("Ввидите название нового JavaScript файла.") . ".js");
        }));
        
        include "./plugins/web/classes/WEBProjectTemplate.php";
        IDE::get()->getProjectManger()->registerTemplate("Web site", new \plugins\web\classes\WEBProjectTemplate());
    }
}

IDE::get()->getPluginsManger()->registerPlugin("web", new webPlugin());