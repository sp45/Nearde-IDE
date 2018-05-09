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

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        IDE::getFormManger()->getForm("OpenProject")->show();
        $this->hide();
    }

}
