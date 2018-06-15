<?php
namespace nd\utils;

use nd;
use nd\modules\IDE;
use php\lang\Process;
use process\ProcessHandle;
use php\io\Stream;
use std;

class NDProcess
{
    /**
     * @var string 
     */
    private $exec;
    
    /**
     * @var string 
     */
    private $dir;
    
    /**
     * @var bool 
     */
    private $started = false;
    
    /**
     * @var Process
     */
    private $process;

    /**
     * @var ProcessHandle
     */
    private $processHandle;

    /**
     * NDProcess constructor.
     * @param string $exec
     * @param string $dir
     */
    public function __construct(string $exec, string $dir)
    {
        $this->exec = $exec;
        $this->dir  = $dir;
        
        if (IDE::isWin()) $prefix = "cmd.exe /c chcp 65001 > nul &";
        
        $this->process = new Process(explode(" ", trim($prefix . " " . $this->exec)), $this->dir);
    }

    /**
     * @return $this
     */
    public function start()
    {
        $this->process = $this->process->start();
        $this->processHandle = new ProcessHandle($this->process);
        $this->started = true;
        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function info()
    {
        return $this->processHandle->info();
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function pid()
    {
        return $this->processHandle->pid();
    }

    /**
     * @param bool $force
     * @return bool
     * @throws \Exception
     */
    public function destroy(bool $force = false)
    {
        if ($force)
            return $this->processHandle->destroy();
        return $this->processHandle->destroyForcibly();
    }

    /**
     * @return array|ProcessHandle[]
     * @throws \Exception
     */
    public function children()
    {
        return $this->processHandle->children();
    }

    /**
     * @return array|ProcessHandle[]
     * @throws \Exception
     */
    public function descendants()
    {
        return $this->processHandle->descendants();
    }

    /**
     * @return \php\io\Stream
     */
    public function getError()
    {
        return $this->process->getError();
    }

    /**
     * @return mixed
     */
    public function getExitValue()
    {
        return $this->process->getExitValue();
    }
    
    /**
     * @return bool 
     */
    public function isStarted()
    {
        return $this->started;
    }
    
    /**
     * @return Stream
     */
    public function getInput()
    {
        return $this->process->getInput();
    }
    
    /**
     * @return Stream
     */
    public function getOutput()
    {
        return $this->process->getOutput();
    }
    
    /**
     * @return Stream
     */
    public function getDir() : string
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getCommand() : string
    {
        return $this->exec;
    }

    /**
     * @return ProcessHandle
     */
    public function getProcessHandle(): ProcessHandle
    {
        return $this->processHandle;
    }
}