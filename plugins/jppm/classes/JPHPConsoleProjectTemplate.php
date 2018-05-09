<?php
namespace plugins\jppm\classes;

use std;
use facade\Json;
use nd;

class JPHPConsoleProjectTemplate extends ProjectTemplate
{
    public function __construct()
    {
        $this->registerCommand("run", function ($path) {
            return execute("cmd.exe /c cd $path && jppm start");
        });
        
        $this->registerCommand("build", function ($path) {
            return execute("cmd.exe /c cd $path && jppm build");
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
        parent::makeProject($project);
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
    }
}