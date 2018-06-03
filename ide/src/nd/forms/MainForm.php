<?php
namespace nd\forms;
use Error;
use facade\Json;
use php\framework\Logger;
use std, gui, framework, nd;
use nd\modules\IDE;
use php\lib\fs;

use nd\utils\project;
use php\gui\event\UXWindowEvent;
use php\gui\event\UXEvent;

class MainForm extends AbstarctIDEForm
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
            IDE::dialog("Файл не является проектом Nearde-IDE");
            $this->doButtonAltAction();
            return;
        }
        
        $json = Json::fromFile($path);
        
        try {
            $project = new project(fs::parent($path), $json['name'], new $json['template']);
        } catch (Error $e) {
            IDE::dialog("Не известный тип проекта. Не удалось открыть проект.");
            Logger::warn('Error open project, trace :');
            echo $e->getTraceAsString();
            Logger::warn('Error message :');
            echo $e->getMessage();
            return;
        }
        
        $project->loadConfig($path);
        IDE::get()->getFormManger()->getForm("Project")->openProject($project);
        $this->hide();
    }
}