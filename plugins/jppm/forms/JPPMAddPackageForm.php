<?php
namespace plugins\jppm\forms;

use php\gui\designer\UXCodeAreaScrollPane;
use php\gui\UXRichTextArea;
use std, gui, framework, nd;


class JPPMAddPackageForm extends UXForm
{
    /**
     * @var NDLog 
     */
    private $log;
    
    private $path;

    function doShow()
    {    
        $this->log = new NDLog;
        $this->log->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1
        ];
        $this->panel->add($this->log);
        
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
        $this->log->reCreate(IDE::createProcess("jppm add {$this->edit->text}@{$this->editAlt->text}", $this->path)->start());
    }

}
