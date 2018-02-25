<?php
namespace projects;

use std;
use app;
use framework;
use \php\lang\Process;

class ProjectType 
{
    /**
     * @var Process
     */
    private $process;
    
    function __construct()
    {
        Logger::info("Load new project type");
    }
    
    public function buildProcess(array $command, $path) : Process
    {
        $os = MainModule::getOS();
        
        $this->process = new Process($command, $path);
        
        return $this->process;
    }
    
    public function getProcess() : Process
    {
        return $this->process;
    }
    
    public function getJars()
    {
        $lib = fs::abs("./lib");
        $dir = new File($lib);
        foreach ($dir->findFiles() as $one)
        {
            if ($one->isFile())
            {
                $libs[] = fs::pathNoExt($one) . ".jar";
            }
        }
        
        return $libs;
    }
}