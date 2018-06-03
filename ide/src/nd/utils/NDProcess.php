<?php
namespace nd\utils;

use nd;
use nd\modules\IDE;
use php\lang\Process;
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
        $this->started = false;
        return $this;
    }
    
    /**
     * @return Stream
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
}