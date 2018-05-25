<?php
namespace nd\forms;

use Error;
use std, gui, framework, nd;


class NeardeSettingsForm extends AbstarctIDEForm
{
    private $theme = [
        'Светлая' => 'light',
        'Тёмная' => 'dark',
    ];
    
    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $config['settings']['projectPath'] = $this->edit->text;
        $t = $config['settings']['style'];
        $config['settings']['style'] = $this->theme[$this->combobox->value];
        IDE::get()->toConfig($config);
        
        if ($this->theme[$this->combobox->value] != $t)
            if (IDE::confirmDialog("Тема изменена, перезапустить IDE ?"))
                IDE::restart();
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $this->edit->text = $config['settings']['projectPath'];
        if ($config['settings']['style'] == 'light')
        {
            $this->combobox->value = 'Светлая';
        } else $this->combobox->value = 'Тёмная';
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
