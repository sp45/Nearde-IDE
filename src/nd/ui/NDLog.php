<?php
namespace nd\ui;

use gui;
use php\gui\UXRichTextArea;
use std;
use php\gui\designer\UXCodeAreaScrollPane;

class NDLog extends UXCodeAreaScrollPane
{
    /**
     * @var Process
     */
    private $process;
    
    /**
     * @var UXRichTextArea 
     */
    private $textArea;
    
    public function __construct(Process $process = null)
    {
        $this->textArea = new UXRichTextArea;
        $this->textArea->padding = 8;
        parent::__construct($this->textArea);
        if ($process)
            $this->reCreate($process);
    }
    
    public function reCreate(Process $process)
    {
        $this->textArea->clear();
        $this->process = $process;
        new Thread(function() {
            $this->process->getInput()->eachLine(function($line) {
                uiLater(function() use ($line) {
                    $this->textArea->appendText($line . "\n", '-fx-fill: gray;');
                });
            });

            $this->process->getError()->eachLine(function($line) {
                uiLater(function() use ($line) {
                    $this->textArea->appendText($line . "\n", '-fx-fill: red;');
                });
            });
            
            $exitValue = $this->process->getExitValue();
            
            uiLater(function () use ($exitValue) {
                $this->textArea->appendText("> exit code: " . $exitValue . "\n", '-fx-fill: #BBBBFF;');
            });
        })->start();
    }
}