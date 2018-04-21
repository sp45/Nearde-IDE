<?php
namespace nd\forms;

use std, gui, framework, nd;


class MainForm extends AbstractForm
{

    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $e = null)
    {    
        $this->ver->text = IDE::get()->getVersion();
        $this->label->text = IDE::get()->getName();
    }

    /**
     * @event sandbox_button.action 
     */
    function doSandbox_buttonAction(UXEvent $e = null)
    {    
        IDE::get()->getFormManger()->getForm("SandBox")->show();
        $this->hide();
    }

}
