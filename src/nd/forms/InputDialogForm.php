<?php
namespace nd\forms;

use std, gui, framework, nd;


class InputDialogForm extends AbstractForm
{

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {
        $this->edit->text = null;
        $this->hide();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {
        $this->hide();
    }
    
    public function open(string $text)
    {
        $this->label->text = $text;
        $this->title = $text;
        $this->showAndWait();
        
        return $this->edit->text;
    }

}
