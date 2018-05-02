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
        
        $this->listView->selectedIndex = 1;
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $template = $this->listView->selectedItem[4];
        $name     = $this->edit->text;
        $path     = IDE::get()->getConfig()['settings']['projectPath'];
        
        if (!$name) 
        {
            alert('Заполните имя проекта.');
            return;
        }
        
        if (fs::isDir($path . "/" . $name))
        {
            alert('Папка с таким именем уже используется.');
            return;
        }
        
        $template->makeProject(new project($path . "/" . $name, $name));
        open($path . "/" . $name);
    }

}
