<?php
namespace nd\forms;

use Error;
use facade\Json;
use std, gui, framework, nd;


class MainForm extends AbstractForm
{

    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $e = null)
    {    
        $this->ver->text = IDE::get()->getVersion();
        $this->label->text = IDE::get()->getName();
    }


    /**
     * @event button3.action 
     */
    function doButton3Action(UXEvent $e = null)
    {    
        IDE::getFormManger()->getForm("Settings")->show();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        IDE::getFormManger()->getForm("NewProject")->show();
        $this->hide();
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction()
    {    
        $path = IDE::treeDialog("Файлы формата .ndproject являются проектами.", IDE::get()->getConfig()['settings']['projectPath']);
        if (!$path)
            return;
        
        if (fs::ext($path) != "ndproject")
        {
            alert("Файл не является проектом Nearde-IDE");
            $this->doButtonAltAction();
            return;
        }
        
        $json = Json::fromFile($path);
        
        try {
            $project = new project(fs::parent($path), $json['name'], new $json['template']);
        } catch (Error $e) {
            alert("Не известный тип проекта. Не удалось открыть проект.");
            return;
        }
        
        $project->loadConfig($path);
        IDE::get()->getFormManger()->getForm("Project")->openProject($project);
        $this->hide();
    }

}
