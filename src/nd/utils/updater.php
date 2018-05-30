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
        $this->checkUpdater();
        log::info(get_class($this), "updater -> checking lasted update");
        $lasted = IDE::githubApiQueryGET('/repos/MWStudio/Nearde-IDE/releases')[0];
        
        if (str::startsWith($lasted['tag_name'], 'b'))
            $gitVersion = substr($lasted['tag_name'], 1);
        else $gitVersion = $lasted['tag_name'];
        
        log::info(get_class($this), "updater -> curent version : " . $this->version);
        log::info(get_class($this), "updater -> git version : " . $gitVersion);
        
        if ($this->version >= $gitVersion) return;
        
        IDE::getFormManger()->getForm("Update")->update($gitVersion, $lasted);
    }
    
    public function checkUpdater()
    {
        log::info(get_class($this), 'updater -> checking updater.jar');
        if (fs::exists("./updater.jar")) {
            log::info(get_class($this), 'updater -> updater.jar exists');
            return;
        }
        
        log::info(get_class($this), 'updater -> create updater.jar');
        fs::makeFile('./updater.jar');
        Stream::putContents('./updater.jar', Stream::getContents('res://.data/vendor/updater.jar'));
        log::info(get_class($this), 'updater -> create updater.jar - done');
    }
}