<?php
namespace nd\forms;

use facade\Json;
use std, gui, framework, nd;


class PluginsForm extends AbstractForm
{

    private $selectedPlugin;
    
    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        IDE::upgradeListView($this->listView);
        
        foreach (IDE::get()->getPluginsManger()->getAll() as $name => $plugin)
        {
            $this->listView->items->add([
                $plugin->getName(),
                "Автор : " . $plugin->getAuthor(),
                IDE::image($plugin->getIcon()),
                function () use ($this, $plugin, $name) {
                    $this->showPluginInfo($plugin, $name);
                }
            ]);
        }
    }

    /**
     * @event checkbox.click 
     */
    function doCheckboxClick(UXMouseEvent $e = null)
    {    
        $s = $this->checkbox->selected;
        if (IDE::confirmDialog("Изменить состояние плагина ?"))
        {
            $json = Json::fromFile("./plugins/plugins.json");
            $json[$this->selectedPlugin]['offline'] = !$s;
            Json::toFile("./plugins/plugins.json", $json);
            IDE::dialog("Для того чтобы изменение вступили в силу вам нужно перезапустить " . IDE::get()->getName());
        } else {
            $this->checkbox->selected = !$s;
        }
    }
    
    public function showPluginInfo(Plugin $plugin, string $name)
    {
        $this->selectedPlugin = $name;
        $this->panel->visible = true;
        $this->name->text = "Имя : " . $plugin->getName();
        $this->author->text = "Автор : " . $plugin->getAuthor();
        $this->desc->text = "Описание : " .$plugin->getDscription();
        $this->checkbox->selected = !IDE::get()->getPluginsManger()->getOfflineForPlugin($name);
    }

}
