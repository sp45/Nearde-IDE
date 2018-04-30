<?php
namespace nd\forms;

use std, gui, framework, nd;


class SettingsForm extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        IDE::upgradeListView($this->listView);
        foreach (IDE::getFormManger()->getAllSettingsForm() as $name => $formClass)
        {
            /** @var UXForm $form */
            $form = new $formClass;
            $this->listView->items->add([
                $name,
                $form->title,
                IDE::image($form->icons->toArray()[0]),
                function () use ($this, $form) {
                    try {
                        $form->showInFragment($this->fragment);
                    } catch (IllegalStateException $e) {
                        ;
                    }
                }
            ]);
        }
    }

}
