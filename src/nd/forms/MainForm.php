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
        $this->ver->text = $GLOBALS['ND']->getVersion();
    }

}
