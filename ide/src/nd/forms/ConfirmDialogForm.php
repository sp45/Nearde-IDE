<?php
namespace nd\forms;

use php\gui\event\UXEvent;
use std, gui, framework, nd;


class ConfirmDialogForm extends AbstarctIDEForm
{

    private $res = false;
    
    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {
        $this->res = false;
        $this->hide();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {
        $this->res = true;
        $this->hide();
    }
    
    public function open(string $text)
    {
        $this->label->text = $text;
        $this->showAndWait();
        
        return $this->res;
    }

}
