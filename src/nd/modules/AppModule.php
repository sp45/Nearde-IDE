<?php
namespace nd\modules;

use php\desktop\Runtime;
use std, gui, framework, nd;


class AppModule extends AbstractModule
{

    /**
     * @event action 
     */
    function doAction(ScriptEvent $e = null)
    {    
        foreach (File::of("./libs")->findFiles() as $lib)
        {
            if (fs::ext($lib) == "jar")
                Runtime::addJar(fs::abs($lib));
        }
        
        $GLOBALS['ND'] = new ND();
        $GLOBALS['ND']->init();
    }

}
