<?php
namespace nd\forms;

use std, gui, framework, nd;


class OpenProjectForm extends AbstractForm
{
    /**
     * @var NDTree
     */
    private $tree;
    
    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {
        IDE::getFormManger()->getForm("Main")->show();
        $this->hide();
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->tree = new NDTree;
        $this->tree->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1
        ];
        
        $this->tree->refreshTree(IDE::get()->getConfig()['settings']['projectPath']);
        $this->panel->add($this->tree);
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $item = $this->tree->selectedItems[0];
        if (!$item)
        {
            alert("Выберете файл проекта.");
            return;
        }
        
        $path = $item->value->path;
        
        if (fs::ext($path) != "ndproject")
        {
            alert("Файл не является проектом Nearde-IDE");
            return;
        }
        
        $json = Json::fromFile($path);
        $p = new project(fs::parent($path), $json['name'], new $json['template']);
        $p->loadConfig($path);
        IDE::get()->getFormManger()->getForm("Project")->openProject($p);
        $this->hide();
    }

}
