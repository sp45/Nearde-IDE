<?php
namespace nd\forms;

use Error;
use std, gui, framework, nd;


class NeardeSettingsForm extends AbstractForm
{

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $config['settings']['projectPath'] = $this->edit->text;
        IDE::get()->toConfig($config);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $this->edit->text = $config['settings']['projectPath'];
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $this->dirChooser->execute();
        try {
            $this->edit->text = $this->dirChooser->file->getAbsolutePath();
        } catch (Error $e) {
            ;
        }
    }

}
