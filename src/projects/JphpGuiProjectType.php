<?php
namespace projects;

use bundle\zip\ZipFileScriptStopException;
use std;
use app;
use \php\lang\Process;

class JphpGuiProjectType extends ProjectType
{
    
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
        return "res://.data/img/java-app.png";
    }
    
    public function getSdk()
    {
        return "JphpGuiProjectType.zip";
    }
    
    public function getDescription(){
        return "Программа на JPHP с JavaFX GUI";
    }
    
    public function del($dir){
        // TDO: нужно сделать удаление папки .nearde 
    }
    
    public function run(BuildLog $log, \utils\Project $project, $call = null)
    {
        $classPaths = arr::toList([ 'src', 'src_generated' ], $this->getJars());
        $log->print("> Build args for start your program" , "green");
        $args = [
            'java',
            '-cp',
            str::join($classPaths, File::PATH_SEPARATOR),
            '-XX:+UseG1GC', '-Xms128M', '-Xmx512m', '-Dfile.encoding=UTF-8', '-Djphp.trace=true',
            'org.develnext.jphp.ext.javafx.FXLauncher'
        ];
        $log->print("> Start new process with args :" , "green");
        $log->print("  --> java -cp ... org.develnext.jphp.ext.javafx.FXLauncher" , "gray");
        $log->print("> In directory :" , "green");
        $log->print("  --> " . $project->getDir(), "gray");
            
        $procces = $this->buildProcess($args, $project->getDir()); 
        
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
                    
                $this->del($project->getDir());
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
                 $this->del($project->getDir());
            }
        }
    }
}