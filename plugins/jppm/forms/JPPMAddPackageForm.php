<?php
namespace plugins\jppm\forms;

use php\gui\designer\UXCodeAreaScrollPane;
use php\gui\UXRichTextArea;
use std, gui, framework, nd;


class JPPMAddPackageForm extends UXForm
{

    /**
     * @var UXRichTextArea
     */
    private $textArea;
    
    private $path;

    function doShow()
    {    
        $this->textArea = new UXRichTextArea;
        $this->textArea->padding = 8;
        
        $scroll = new UXCodeAreaScrollPane($this->textArea);
        $scroll->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1
        ];
        $this->panel->add($scroll);
        
        $this->button->classesString .= "accent-btn";
        $this->button->on('click', function () {
            $this->doButtonAction();
        });
        
        $this->buttonAlt->classesString .= "warning-btn";
        $this->buttonAlt->on('click', function () {
            $this->hide();
        });
    }
    
    public function show(string $path)
    {
        $this->path = $path;
        $this->layout = new UXLoader->load(fs::abs("./plugins/jppm/forms/JPPMAddPackageForm.fxml"));
        $this->title = "Добовление пакета.";
        $this->icons->add(IDE::ico("build16.png")->image);
        $this->addStylesheet(".theme/style.fx.css");
        $this->doShow();
        parent::show();
    }

    function doButtonAction()
    {    
        $process = new Process(explode(" ", "cmd.exe /c jppm add {$this->edit->text}@{$this->editAlt->text}"), $this->path)->start();
        new Thread(function() use ($process) {
            $process->getInput()->eachLine(function($line) {
                uiLater(function() use ($line, $textArea) {
                    $this->textArea->appendText($line . "\n", '-fx-fill: gray;');
                });
            });

            $process->getError()->eachLine(function($line) {
                uiLater(function() use ($line, $textArea) {
                    $this->textArea->appendText($line . "\n", '-fx-fill: red;');
                });
            });
            
            $exitValue = $process->getExitValue();
            
            uiLater(function () use ($exitValue) {
                $this->textArea->appendText("> exit code: " . $exitValue . "\n", '-fx-fill: #BBBBFF;');
            });
        })->start();
    }

}
