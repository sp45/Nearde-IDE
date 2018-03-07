<?php
namespace app\forms;

use php\gui\framework\AbstractForm;
use php\gui\event\UXEvent; 


class BuildEnd extends AbstractForm
{

    private $call;
    
    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $this->hide();
    }

    /**
     * @event button3.action 
     */
    function doButton3Action(UXEvent $e = null)
    {    
        open($this->edit->text);
        
        $this->hide();
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $call = $this->call;
        
        $call();
        
        $this->hide();
    }
    
    public function open($dir, $call)
    {
        $this->edit->text = $dir;
        $this->call = $call;
    }

}
