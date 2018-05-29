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
        
        if ($GLOBALS['argv'][1] == "--kill") 
        {
            $pid = $GLOBALS['argv'][2];
            if (IDE::isWin())
            {
                new NDProcess('taskkill //PID ' . $pid, './')->start();
            } else {
                new NDProcess('kill -9 ' . $pid, './')->start();
            }
            
            unset($GLOBALS['argv'][1]);
            unset($GLOBALS['argv'][2]);
        }
        
        if (count($GLOBALS['argv']) == 1)
            $initType = "window";
        else {
            $initType = "console";
            //$this->visible = false;
        }
        $GLOBALS['ND'] = new ND();
        $GLOBALS['ND']->init($initType);
        
    }

}
