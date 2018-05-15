<?php
namespace plugins\jppm\classes;

use gui;
use std;
use facade\Json;
use nd;

class JPHPConsoleProjectTemplate extends ProjectTemplate
{
    public function __construct()
    {
        $this->registerCommand("run", function ($path) {
            return IDE::createProcess("jppm start", $path)->start();
        });
        
        $this->registerCommand("build", function ($path) {
            return IDE::createProcess("jppm build", $path)->start();
        });
        
        $this->regiserGunter("JPPM: Добавление пакета.", IDE::ico("build16.png"), function ($path) {
            IDE::getFormManger()->getForm("JPPMAddPackageForm")->show($path);
        });
    }
    
    public function getName()
    {
        return "JPHP консольное приложение.";
    }
    
    public function getIcon()
    {
        return "./plugins/jphp/data/images/php.png";
    }
    
    public function getDscription()
    {
        return "Написание приложений на jphp.";
    }
    
    public function makeProject($project)
    {
        if (!parent::makeProject($project)) return false;
        $path = $project->getPath();
        $name = $project->getName();
        fs::makeFile(fs::abs($path . "/package.php.yml"));
        Stream::putContents(fs::abs($path . "/package.php.yml"), "name: ". $name ."\nversion: 1.0.0\ndeps:
  jphp-core: '*'

plugins:
- App

app:
  bootstrap: index.php
  encoding: UTF-8

sources:
- src

config:
  build-dir: ./build
  vendor-dir: ./vendor
  archive-dir: ./../
  archive-format: zip");
        fs::makeDir($path . "/src");
        FileUtils::createFile($path . "/src", "index.php", "<?php \n\necho 'Hello, World';");
        return true;
    }
}