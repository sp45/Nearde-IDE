<?php
namespace app\modules;

use php\compress\ZipFile;
use std, gui, framework, app;


class MainModule extends AbstractModule
{
    
    public function getProjects() : IdeProjects
    {
        $ideProjectsClass = new IdeProjects;
        $ideProjectsClass->registerType(new JphpGuiProjectType);
        return $ideProjectsClass;
    }
    
    public static function getOS()
    {
        $osName = System::getProperty('os.name');
        if ($osName == "linux")
            return $osName;
        else {
            $win = strtolower(explode(" ", $osName)[0]);
            if ($win == "windows")
                return $win;
            else 
                return "other";
        }
            
    }
    
    public function installTool(string $name, UXForm $form)
    {
        $form->showPreloader("Установка утилиты " . $name);
        
        $to = fs::abs("./tools/" . $name);
        
        $zip = new ZipFile($to . ".zip");
        $zip->unpack($to);
        
        if ($this->getOS() == "windows")
            $command = 'cmd.exe /c chcp 65001 > nul & cd ' . $to . " && " . "gradlew.bat install";
        else 
            $command = "gradle -P " . $to . " install";
        
        $process = new Process(explode(' ', $command));
        $process = $process->start();
        
        $thread = new Thread(function() use ($name, $process, $form){
            $process->getInput()->eachLine(function($line) use ($name) {
                uiLater(function() use ($line, $name) {
                    echo '[INSTALL]['. $name .'] ' . $line . " \n";
                });
            });

            $process->getError()->eachLine(function($line) use ($name) {
                uiLater(function () use ($line, $name) {
                    echo '[INSTALL]['. $name .'][ERROR] ' . $line . " \n";
                }); 
            });
            
            $exitValue = $process->getExitValue();
            uiLater(function () use ($exitValue, $name, $form) {
                
                if ($exitValue == 0)
                {
                    $form->hidePreloader();
                    return true;
                } else {
                    pre("Не удалось установить утилиту " . $name);
                    $form->hidePreloader();
                    return false;
                }
            });
        });
        
        $thread->start();
    }
}
