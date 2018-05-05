<?php
namespace nd\forms;

use Error;
use std, gui, framework, nd;


class NeardeSettingsForm extends AbstractForm
{
    private $thems = [
        "chrome",
        "clouds",
        "crimson_editor",
        "dawn",
        "dreamweaver",
        "eclipse",
        "github",
        "solarized_light",
        "textmate",
        "tomorrow",
        "xcode",
        "kuroir",
        "katzenmilch",
        "ambiance",
        "chaos",
        "clouds_midnight",
        "cobalt",
        "idle_fingers",
        "kr_theme",
        "merbivore",
        "merbivore_soft",
        "mono_industrial",
        "monokai",
        "pastel_on_dark",
        "solarized_dark",
        "terminal",
        "tomorrow_night",
        "tomorrow_night_blue",
        "tomorrow_night_bright",
        "tomorrow_night_eighties",
        "twilight",
        "vibrant_ink"
    ];
    
    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $config['settings']['projectPath'] = $this->edit->text;
        $config['settings']['editorStyle'] = $this->combobox->value;
        IDE::get()->toConfig($config);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $this->edit->text = $config['settings']['projectPath'];
        $this->combobox->value = $config['settings']['editorStyle'];
        $this->combobox->items->addAll($this->thems);
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
