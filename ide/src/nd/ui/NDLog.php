<?php
namespace nd\ui;

use nd;
use framework;
use gui;
use php\gui\UXRichTextArea;
use php\io\IOException;
use std;
use php\gui\designer\UXCodeAreaScrollPane;
use php\lang\Thread;
use nd\utils\NDProcess;

class NDLog extends UXCodeAreaScrollPane
{
    /**
     * @var UXRichTextArea
     */
    private $textArea;
    
    private $commandBuffer;
    private $consoleBuffer;
    
    private $processRuning = false;
    
    private $dir;
    private $line;
    
    /**
     * @var NDProcess
     */
    private $process;
    
    
    public function __construct(string $dir)
    {    
        $this->dir = $dir;
        $this->textArea = new UXRichTextArea;
        $this->textArea->padding = 8;
        $this->textArea->on('keyUp', function (UXKeyEvent $e) {
            $this->doConsoleWrite($e);
        });
        parent::__construct($this->textArea);
    }
    
    public function doConsoleWrite(UXKeyEvent $e)
    {
        if (str::length($this->textArea->text) < $this->consoleBuffer['length'])
        {
            $this->restoreFromBuffer();
            return;
        }
             
        if ($e->codeName == "Backspace")
        {
            $this->commandBuffer = substr($this->commandBuffer, 0, -1);
            return;
        }
        
        if ($e->codeName == "Enter")
        {
            $this->restoreFromBuffer();
            $this->addConsole($this->commandBuffer . "\n", 'blue');
            if (!$this->processRuning)
                $this->parseCommand($this->commandBuffer);
            else {
                $this->process->getOutput()->write($this->commandBuffer . " \n");
                $this->process->getOutput()->flush();
            }
            $this->commandBuffer = null;
            return;
        }
        
        $this->commandBuffer = trim(substr($this->textArea->text, $this->consoleBuffer['length']));
    }
    
    public function addConsole(string $text, string $color = '#333', $addToBuffer = true, $customCss = null)
    {
        $this->textArea->appendText($text, '-fx-fill:' . $color . '; ' . $customCss);
        if (!$addToBuffer) return;
        
        $this->line++;
        $this->scrollY = $this->line * 25;
        
        $this->consoleBuffer['length'] = str::length($this->textArea->text);
        $this->consoleBuffer['out'][] = [
            'text'  => $text,
            'color' => $color,
            'style' => $customCss
        ];
    }
    
    public function restoreFromBuffer()
    {
        $this->textArea->clear();
        foreach ($this->consoleBuffer['out'] as $line)
            $this->addConsole($line['text'], $line['color'], false, $line['style']);
    }
    
    public function printUserAndDir()
    {
        $this->addConsole("\n");
        $this->addConsole(System::getProperty('user.name'), 'blue');
        $this->addConsole(" : ");
        $this->addConsole(fs::abs($this->dir), 'green');
        $this->addConsole(" $ ", 'blue');
    }
    
    public function parseCommand(string $command)
    {
        if (!$command) {
            $this->printUserAndDir();
            return;
        }
        
        switch (explode(' ', $command)[0]) {
            case 'cd' : 
                if (fs::exists(explode(' ', $command)[1]))
                {
                    $this->dir = explode(' ', $command)[1];
                    $this->printUserAndDir();
                    return;
                }
                
                $dir = explode(' ', $command)[1];
                $file = File::of($this->dir .'/'. $dir);
                $this->dir = $file->getAbsolutePath();
                $this->printUserAndDir();
            break;
            case 'mkdir' : 
                if (explode(' ', $command)[1])
                    fs::makeDir(fs::abs($this->dir) . '/' . explode(' ', $command)[1]);
                    
                $this->printUserAndDir();
            break;
            case 'clear' : 
                $this->textArea->clear();
                $this->consoleBuffer = null;
                $this->printUserAndDir();
            break;
            default: 
                $this->runProcess($command, function () {
                    $this->printUserAndDir();
                });
        }
        
    }  
    
    public function runProcess($process, callable $onExit = null)
    {
        try {

            /** @var NDProcess $process */
            if (is_string($process))
                $process = new NDProcess($process, $this->dir);

            if (!($process instanceof NDProcess)) return;

            $this->process = $process;
            if (!$process->isStarted())
                $process->start();

        } catch (IOException $exception) {
            $this->addConsole('Error run program ' . $process->getCommand() . "\n", 'red');
            $this->printUserAndDir();
            return;
        }
        
        $this->processRuning = true;
        
        (new Thread(function() use ($onExit) {
            $this->process->getInput()->eachLine(function($line){
                uiLater(function() use ($line) {
                    $this->addConsole($line . "\n");
                });
            });
        
            $this->process->getError()->eachLine(function($line){
                uiLater(function () use ($line) {
                    $this->addConsole($line  . "\n", 'red');
                }); 
            });
                    
            $exitValue = $this->process->getExitValue();
            uiLater(function () use ($onExit, $exitValue) {
                $this->processRuning = false;
                call_user_func($onExit, $exitValue);
            });
        }))->start();
    }      
}