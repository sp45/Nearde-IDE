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
                function () use ($this, $formClass) {
                    $this->showForm($formClass);
                }
            ]);
        }
    }
    
    private function showForm($formClass)
    {
        $f_pos = $this->fragment->position;
        $f_size = $this->fragment->size;
        $this->fragment->free();
        
        $fragment = new UXFragmentPane;
        $fragment->id = "fragment";
        $fragment->position = $f_pos;
        $fragment->size = $f_size;
        new $formClass()->showInFragment($fragment);
        $this->add($fragment);
        Logger::info("Load $formClass to settings form.");
    }

}
