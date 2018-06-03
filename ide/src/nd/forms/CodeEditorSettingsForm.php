<?php
namespace nd\forms;

use php\gui\event\UXEvent;
use php\gui\event\UXMouseEvent;
use php\gui\event\UXWindowEvent;
use std, gui, framework, nd;

use nd\modules\IDE;
use nd\ui\NDCode;

class CodeEditorSettingsForm extends AbstarctIDEForm
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
     * @var NDCode
     */
    private $editor;
    
    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {
        $config = IDE::get()->getConfig();
        $config['settings']['editor']['style'] = $this->combobox->value;
        $config['settings']['editor']['invisibles']  = $this->checkbox->selected;
        $config['settings']['editor']['font_size']   = (int) $this->slider->value;
        IDE::get()->toConfig($config);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $this->combobox->value = $config['settings']['editor']['style'];
        $this->combobox->items->addAll($this->thems);
        $this->checkbox->selected = $config['settings']['editor']['invisibles'];
        $this->slider->value = $config['settings']['editor']['font_size'];
        $this->editor = new NDCode("# Code on python\nprint \"Hello, World!\"", "python", true);
        $this->editor->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1,
        ];
        
        $this->panel->add($this->editor);
    }

    /**
     * @event combobox.action 
     */
    function doComboboxAction(UXEvent $e = null)
    {    
        $this->editor->setTheme($this->combobox->value);
    }

    /**
     * @event checkbox.click 
     */
    function doCheckboxClick(UXMouseEvent $e = null)
    {    
        $this->editor->setShowInvisibles($this->checkbox->selected);
    }

    /**
     * @event slider.mouseUp 
     */
    function doSliderMouseUp(UXMouseEvent $e = null)
    {    
        $this->editor->setFontSize($this->slider->value);
    }

}
