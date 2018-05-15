<?php
namespace nd\forms;

use std, gui, framework, nd;


class NewProjectForm extends AbstractForm
{

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
        IDE::upgradeListView($this->listView);
        foreach (IDE::get()->getProjectManger()->getAll() as $template)
        {
            $this->listView->items->add([
                $template->getName(),
                $template->getDscription(),
                IDE::image($template->getIcon()),
                null,
                $template
            ]);
        }
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $template = $this->listView->selectedItem[4];
        $name     = str_replace(" ", "_", trim($this->edit->text));
        $path     = IDE::get()->getConfig()['settings']['projectPath'];
        
        if (!$name) 
        {
            IDE::dialog('Заполните имя проекта.');
            return;
        }
        
        if (fs::isDir($path . "/" . $name))
        {
            IDE::dialog('Папка с таким именем уже используется.');
            return;
        }
        
        $project = new project($path . "/" . $name, $name, $template);
        
        if (!$template->makeProject($project)) return;
        IDE::getFormManger()->getForm("Project")->openProject($project);
        $this->hide();
    }

    /**
     * @event listView.action 
     */
    function doListViewAction(UXEvent $e = null)
    {    
        $this->button->enabled = true;
    }

}
