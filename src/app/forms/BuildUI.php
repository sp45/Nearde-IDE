<?php
namespace app\forms;

use ui\BuildLog;
use php\gui\framework\AbstractForm;
use php\gui\event\UXWindowEvent; 


class BuildUI extends AbstractForm
{
    
    /**
     * @var BuildLog
     */
    private $log;
    
    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->log = new BuildLog();
        
        $ui = $this->log->makeUI();
        
        $this->panel->add($ui);
    }
    
    public function print($text, $color = "#333")
    {
        $this->log->print($text, $color);
    }

}
