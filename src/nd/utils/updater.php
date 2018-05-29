<?php
namespace nd\utils;

use framework;
use std;
use nd;

class updater 
{
    private $version;
    
    public function __construct(int $version)
    {
        $this->version = $version;
    }
    
    public function checkUpdate()
    {
        Logger::info("updater -> checking lasted update");
        $lasted = IDE::githubApiQueryGET('/repos/MWStudio/Nearde-IDE/releases')[0];
        
        if (str::startsWith($lasted['tag_name'], 'b'))
            $gitVersion = substr($lasted['tag_name'], 1);
        else $gitVersion = $lasted['tag_name'];
        
        Logger::info("updater -> curent version : " . $this->version);
        Logger::info("updater -> git version : " . $gitVersion);
        
        if ($this->version >= $gitVersion) return;
        
        IDE::getFormManger()->getForm("Update")->update($gitVersion, $lasted);
    }
}