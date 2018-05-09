<?php

namespace plugins\jphp;
use gui;
use std;
use nd;

class jppmPlugin extends Plugin
{
    public function getName()
    {
        return "JPPM";
    }
    
    public function getIcon()
    {
        return "./plugins/jppm/data/images/package.png";
    }
    
    public function getDscription()
    {
        return "JPPM - менеджер пакетов для jphp, например, npm (js) или composer (php). JPPM поможет вам создавать и запускать приложения на jphp.";
    }
    
    public function getAuthor()
    {
        return "MWStudio";
    }
    
    public function onIDEStarting()
    {
        if (!IDE::get()->getPluginsManger()->getPlugin("PHP"))
        {
            alert("Плагин JPPM не может нормально работать без плагина php. Инициализация плагина прервана.");
            return;
        }
        
        // include external classes
        include fs::abs("./plugins/jppm/classes/JPHPConsoleProjectTemplate.php");
        include fs::abs("./plugins/jppm/forms/JPPMAddPackageForm.php");
        
        IDE::getFormManger()->registerForm("JPPMAddPackageForm", \plugins\jppm\forms\JPPMAddPackageForm::class);
        IDE::get()->getProjectManger()->registerTemplate("JPHP Console", new \plugins\jppm\classes\JPHPConsoleProjectTemplate());
    }
}

IDE::get()->getPluginsManger()->registerPlugin("jppm", new jppmPlugin());
