<?php
namespace nd\utils;

use nd;
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
    
    public function __construct(string $exec, string $dir)
    {
        $this->exec = $exec;
        $this->dir  = $dir;
        
        if (IDE::isWin()) $prefix = "cmd.exe /c chcp 65001 > nul &";
        
        $this->process = new Process(explode(" ", trim($prefix . " " . $this->exec)), $this->dir);
    }
    
    public function start()
    {
        $this->process = $this->process->start();
        return $this;
    }
    
    /**
     * @return Stream
     */
    public function getError()
    {
        return $this->process->getError();
    } 
    
    public function getExitValue()
    {
        return $this->process->getExitValue();
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
    
    public function getCommand() : string
    {
        return $this->exec;
    }
}