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

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $this->zipChooser->execute();
        $file = $this->zipChooser->file;
        if (!$file) {
            IDE::dialog("Файл не выбран.");
            return;
        }
        
        $tempDir = fs::abs("./plugins/temp/" . substr(md5(Time::now()), 5));
        fs::makeDir($tempDir);
        if (!IDE::unpackDialog((string) $file, $tempDir)) return;
        
        if (!fs::exists($tempDir . "/.ndp"))
        {
            IDE::dialog("Архив не является плагином для " . IDE::get()->getName());
            FileUtils::delete($tempDir);
            return;
        }
        
        $ini = new IniStorage(fs::abs($tempDir . "/.ndp"));
        
        $pluginData = $ini->toArray()[''];
        $pluginDir = fs::abs("./plugins/" . $pluginData['dir']);
        if (fs::exists($pluginDir)) {
            IDE::dialog("Плагин уже установлен.");
            FileUtils::delete($tempDir);
            return;
        }
        
        fs::makeDir($pluginDir);
        FileUtils::copy($tempDir, $pluginDir);
        FileUtils::delete($tempDir);
        $json = Json::fromFile("./plugins/plugins.json");
        $json[strtoupper($pluginData['dir'])] = $pluginData;
        Json::toFile("./plugins/plugins.json", $json);
        
        IDE::dialog("Плагин успешно установлен! Для его активации нужно перезагрузить " . IDE::get()->getName());
        if (IDE::confirmDialog("Перезапустить " . IDE::get()->getName() . " ?"))
            IDE::restart();
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        if (!IDE::confirmDialog("Точно удалить плагин ?")) return;
        $json = Json::fromFile("./plugins/plugins.json");
        FileUtils::delete("./plugins/" . $json[$this->selectedPlugin]['dir']);
        unset($json[$this->selectedPlugin]);
        Json::toFile("./plugins/plugins.json", $json);
        IDE::dialog("Плагин {$this->selectedPlugin} успешно удалён. Для продолжение работы нужно перезапустить " . IDE::get()->getName());
        if (IDE::confirmDialog("Перезапустить " . IDE::get()->getName() . " ?"))
            IDE::restart();
    }
    
    public function showPluginInfo(Plugin $plugin, string $name)
    {
        $this->selectedPlugin = $name;
        $this->panel->visible = true;
        $this->name->text = "Имя : " . $plugin->getName();
        $this->author->text = "Автор : " . $plugin->getAuthor();
        $this->version->text = "Версия : " . $plugin->getVersion();
        $this->desc->text = "Описание : " . $plugin->getDscription();
        $this->checkbox->selected = !IDE::get()->getPluginsManger()->getOfflineForPlugin($name);
    }

}
