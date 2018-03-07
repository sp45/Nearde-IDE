<?php
namespace projects;

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
    
    public function build(\utils\Project $project)
    {
        $this->makePhb($project);
        
        $ui = app()->getForm("BuildUI");
        
        $ui->show();
        
        $libs = File::of("./lib/");
        fs::makeDir($project->getDir() . "/build/dist/lib/");
        fs::makeDir($project->getDir() . "/build/dist/gen/");
        fs::makeFile($project->getDir() . "/build.xml");
        
        fs::makeFile($project->getDir() . "/build/dist/gen/META-INF/services/php.runtime.ext.support.Extension");
        Stream::putContents($project->getDir() . "/build/dist/gen/META-INF/services/php.runtime.ext.support.Extension", "org.develnext.jphp.json.JsonExtension\n\norg.develnext.jphp.ext.xml.XmlExtension\n\norg.develnext.jphp.ext.javafx.JavaFXExtension\n\norg.develnext.jphp.ext.gui.desktop.GuiDesktopExtension\n\norg.develnext.jphp.zend.ext.ZendExtension\n\norg.develnext.jphp.parser.ParserExtension\n\norg.develnext.jphp.ext.sql.SqlExtension\n\norg.develnext.jphp.ext.systemtray.SystemTrayExtension\n\norg.develnext.jphp.ext.zip.ZipExtension");
        
        $build = Stream::getContents("res://build/build.xml");
        $build = str_replace("%NAME%", $project->getName(), $build);
        
        foreach ($libs->findFiles() as $file)
        {
            $f = new File($project->getDir() . "/build/dist/lib/" . fs::name($file));
            $zips .= '<zipfileset src=\'${dist}/lib/'.fs::name($file)."' /> \n";
            $ui->print(":aplay-jar " . fs::name($file), "gray");
            fs::makeFile($project->getDir() . "/build/dist/lib/" . fs::name($file));
            fs::copy($file, $f);
        }
        
        $build = str_replace("%ZIPFILESET%", $zips, $build);
        
        Stream::putContents($project->getDir() . "/build.xml", $build);
        
        $env = MainModule::makeEnv();
        
        if (MainModule::getOS() == "windows")
        {
          $args = [
              'cmd.exe',
              '/c',
              $env['ANT_HOME'] . '/bin/ant.bat',
              'onejar'
          ];  
        } else {
            $args = [
              'ant',
              'onejar'
          ];  
        }
        
        $procces = $this->buildProcess($args, $project->getDir()); 
        $p = $procces->start();
        
        $t = new Thread(function () use ($p, $ui, $call, $project) {
            $p->getInput()->eachLine( function($line) use ($ui) {
                uiLater(function () use ($line, $ui) {
                    $ui->print($line);
                });
            });
            
            $p->getError()->eachLine( function($line)  use ($ui) {
                uiLater(function () use ($line, $ui) {
                    $ui->print($line, "red");
                });
            });
            
            $exitValue = $p->getExitValue();
            uiLater(function () use ($ui, $exitValue, $call, $project) {
                if ($exitValue == 0)
                {
                    $form = app()->getForm("BuildEnd");
                    $form->open(fs::abs($project->getDir() . "/build/dist/"), function () use ($project) {
                        open(fs::abs($project->getDir() . "/build/dist/" . $project->getName() . ".jar"));
                    });
                    $ui->hide();
                    $form->show();
                } else $ui->print("> exit code: " . $exitValue);
                
                if ($call)
                    $call();
            });
        });
        $t->start();
        
    }
    
    public function makePhb(\utils\Project $project)
    {
        
    }
}