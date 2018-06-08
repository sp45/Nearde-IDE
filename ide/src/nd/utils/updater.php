<?php
namespace nd\utils;

use framework;
use php\framework\Logger;
use php\io\IOException;
use std;
use nd;

use nd\modules\IDE;
use php\lib\str;
use php\io\Stream;
use php\lib\fs;

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


    public function checkUpdater()
    {
        Logger::info('updater -> checking updater.jar');
        if (fs::exists("./updater.jar")) {
            Logger::info('updater -> updater.jar exists');
            return;
        }
        
        Logger::info('updater -> create updater.jar');
        fs::makeFile('./updater.jar');
        try {
            Stream::putContents('./updater.jar', Stream::getContents('res://.data/vendor/updater.jar'));
        } catch (IOException $e) {
            ;
        }
        Logger::info('updater -> create updater.jar - done');
    }
}