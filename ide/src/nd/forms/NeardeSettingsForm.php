<?php
namespace nd\forms;

use Error;
use php\gui\event\UXEvent;
use php\gui\event\UXWindowEvent;
use nd\modules\IDE;
use std, gui, framework, nd;


class NeardeSettingsForm extends AbstarctIDEForm
{
    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {
        /** @var nd\ND $ide */
        $ide = IDE::get();
        $config = $ide->getConfig();

        $config['settings']['projectPath'] = $this->edit->text;
        $config['settings']['style'] = $ide->getThemeManger()->getByName($this->combobox->value)->getID();

        $ide->toConfig($config);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        /** @var nd\ND $ide */
        $ide = IDE::get();

        $config = $ide->getConfig();
        $this->edit->text = $config['settings']['projectPath'];
        $this->combobox->value = $ide->getThemeManger()->get($config['settings']['style'])->getName();

        foreach ($ide->getThemeManger()->getAll() as $theme)
            $this->combobox->items->add($theme->getName());
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
