<?php
namespace nd\modules;

use std, gui, framework, nd;


class AppModule extends AbstractModule
{

    /**
     * @event action 
     */
    function doAction(ScriptEvent $e = null)
    {    
        $GLOBALS['ND'] = new ND();
        $GLOBALS['ND']->init();
    }

}
