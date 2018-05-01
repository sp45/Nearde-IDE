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
        
        if (!IDE::get()->isDev())
            $this->sandbox_button->hide();
    }

    /**
     * @event sandbox_button.action 
     */
    function doSandbox_buttonAction(UXEvent $e = null)
    {    
        IDE::getFormManger()->getForm("SandBox")->show();
        $this->hide();
    }

    /**
     * @event button3.action 
     */
    function doButton3Action(UXEvent $e = null)
    {    
        IDE::getFormManger()->getForm("Settings")->show();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        IDE::getFormManger()->getForm("NewProject")->show();
        $this->hide();
    }

}
