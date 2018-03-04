<?php
namespace app\modules;

use utils\Project;
use app\modules\MainModule;
use facade\Json;
use std, gui, framework, app;


class AppModule extends AbstractModule
{

    /**
     * @event action 
     */
    function doAction(ScriptEvent $e = null)
    {    
        if (fs::ext($GLOBALS['argv'][1]) == "nrd")
        {
            $project = new \utils\Project;
            if (!$project->Open(fs::parent($GLOBALS['argv'][1]), fs::nameNoExt($GLOBALS['argv'][1])))
            {
                app()->showForm("MainForm");
            }
        } else {
            app()->showForm("MainForm");
        }
    }

}
