<?php
namespace projects;

use build\JphpGuiBuildType;
use php\lang\Thread;
use php\io\Stream;
use php\io\File;
use php\lib\fs;
use build\BuildUI;
use app\modules\MainModule;
use bundle\zip\ZipFileScriptStopException;
use std;
use app;
use \php\lang\Process;

class JphpGuiProjectType extends ProjectType
{
    
    public function onRegister()
    {
        $this->registerBuildType(new JphpGuiBuildType());
    }
    
    public function getName()
    {
        return "JPHP GUI прилолжение";
    }
    
    public function getId()
    {
        return __CLASS__;
    }
    
    public function getIcon()
    {
        return "res://.data/img/jphp-icon.png";
    }
    
    public function getSdk()
    {
        return "JphpGuiProjectType.zip";
    }
    
    public function getDescription(){
        return "Программа на JPHP с JavaFX GUI";
    }
    
    public function run(BuildLog $log, \utils\Project $project, $call = null)
    {
        $classPaths = arr::toList([ 'src', 'src_generated' ], $this->getJars());
        $args = [
            'java',
            '-cp',
            str::join($classPaths, File::PATH_SEPARATOR),
            '-XX:+UseG1GC', '-Xms128M', '-Xmx512m', '-Dfile.encoding=UTF-8', '-Djphp.trace=true',
            'org.develnext.jphp.ext.javafx.FXLauncher'
        ];
        $log->print("> java -cp ... org.develnext.jphp.ext.javafx.FXLauncher" , "green");
        $log->print("  --> " . $project->getDir(), "gray");
            
        $procces = $this->buildProcess($args, $project->getDir(), MainModule::makeEnv()); 
        
        $p = $procces->start();
        
        $t = new Thread(function () use ($p, $log, $call, $project) {
            $p->getInput()->eachLine( function($line) use ($log) {
                uiLater(function () use ($line, $log) {
                    $log->print($line);
                });
            });
            
            $p->getError()->eachLine( function($line)  use ($log) {
                uiLater(function () use ($line, $log) {
                    $log->print($line, "red");
                });
            });
            
            $exitValue = $p->getExitValue();
            uiLater(function () use ($log, $exitValue, $call, $project) {
                if ($exitValue != 0)
                    $log->print("> exit code: " . $exitValue);
                else 
                    $log->hide();
                
                if ($call)
                    $call();
            });
        });
        $t->start();
    }
    
    public function stop(\utils\Project $project, $call)
    {
        $pid = Stream::getContents($project->getDir() . "/application.pid");
        if ($pid)
        {
            $os = MainModule::getOS();
            if ($os == "windows") {
                $result = `taskkill /PID $pid /f`;
            } else {
                $result = `kill -9 $pid`;
            }
            if (!$result) {
                var_dump("Не удалось остановить приложение. pid : " . $pid);
            } else {
                 $call();
            }
        }
    }
    
    public function build(\utils\Project $project)
    {
        
    }
}