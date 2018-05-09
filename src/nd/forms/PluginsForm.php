<?php
namespace nd\forms;

use std, gui, framework, nd;


class PluginsForm extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        IDE::upgradeListView($this->listView);
        
        foreach (IDE::get()->getPluginsManger()->getAll() as $plugin)
        {
            $this->listView->items->add([
                $plugin->getName(),
                "Автор : " . $plugin->getAuthor(),
                IDE::image($plugin->getIcon()),
                function () use ($this, $plugin) {
                    $this->showPluginInfo($plugin);
                }
            ]);
        }
    }
    
    public function showPluginInfo(Plugin $plugin)
    {
        $this->panel->visible = true;
        $this->name->text = $plugin->getName();
        $this->desc->text = $plugin->getDscription();
    }

}
