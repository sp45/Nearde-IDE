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
        
        $this->progressBar->progress = -100;
        
        $ui = $this->log->makeUI();
        
        $this->panel->add($ui);
    }
    
    public function print($text, $color = "#fff")
    {
        $this->log->print($text, $color);
    }
    
    public function hide()
    {
        $this->progressBar->progress = 100;
        if ($this->checkbox->selected)
        {
            parent::hide();
        }
    }

}
