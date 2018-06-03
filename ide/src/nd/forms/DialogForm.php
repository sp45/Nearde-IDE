<?php
namespace nd\forms;

use php\gui\event\UXEvent;
use std, gui, framework, nd;


class DialogForm extends AbstarctIDEForm
{
    
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
        $this->showAndWait();
    }

}
