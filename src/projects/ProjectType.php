<?php
namespace projects;

use php\gui\framework\behaviour\custom\AbstractBehaviour;
use build\BuildType;
use std;
use app;
use framework;
use \php\lang\Process;

abstract class ProjectType 
{
    /**
     * @var Process
     */
    private $process;
    
    private $buildTypes;
    
    public function __construct()
    {
        $this->onRegister();
    }
    
    public function buildProcess(array $command, $path) : Process
    {
        $this->process = new Process($command, $path);
        
        return $this->process;
    }
    
    public function getProcess() : Process
    {
        return $this->process;
    }
    
    public function getJars()
    {
        
        $lib = fs::abs("./lib/");
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
    
    public function registerBuildType(BuildType $type)
    {
        if ($this->buildTypes[$type->getId()]) return;
        
        $this->buildTypes[$type->getId()] = $type;
    }
    
    public function getBuildType($id)
    {
        if (!$this->buildTypes[$id]) return;
        
        return $this->buildTypes[$id];
    }
    
    public function getAllBuildTypes()
    {
        return $this->buildTypes;
    }
    
    abstract function onRegister();
}