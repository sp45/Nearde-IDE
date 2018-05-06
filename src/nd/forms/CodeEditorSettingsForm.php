<?php
namespace nd\forms;

use std, gui, framework, nd;


class CodeEditorSettingsForm extends AbstractForm
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
     * @vae NDCode
     */
    private $editor;
    
    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {
        $config = IDE::get()->getConfig();
        $config['settings']['editorStyle'] = $this->combobox->value;
        IDE::get()->toConfig($config);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $config = IDE::get()->getConfig();
        $this->combobox->value = $config['settings']['editorStyle'];
        $this->combobox->items->addAll($this->thems);
        $this->editor = new NDCode("<?php\necho 'Hello, World!';", "php", true);
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

}
