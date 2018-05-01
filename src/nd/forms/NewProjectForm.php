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
                IDE::image($template->getIcon())
            ]);
        }
    }

}
