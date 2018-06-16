<?php
namespace nd\forms;

use php\framework\Logger;
use php\gui\event\UXWindowEvent;
use nd\modules\IDE;
use php\gui\layout\UXFragmentPane;
use std, gui, framework, nd;


class SettingsForm extends AbstarctIDEForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        IDE::upgradeListView($this->listView, 1);
        foreach (IDE::getFormManger()->getAllSettingsForm() as $name => $formClass)
        {
            /** @var UXForm $form */
            $form = new $formClass;
            $this->listView->items->add([
                $name,
                $form->title,
                IDE::image($form->icons->toArray()[0]),
                function () use ($formClass) {
                    $this->showForm($formClass);
                },
                $formClass
            ]);
        }

        $this->showForm($this->listView->items->toArray()[0][4]);
    }
    
    private function showForm($formClass)
    {
        $f_pos = $this->fragment->position;
        $f_size = $this->fragment->size;
        $this->fragment->free();
        
        $fragment = new UXFragmentPane();
        $fragment->id = "fragment";
        $fragment->position = $f_pos;
        $fragment->size = $f_size;

        $formNode = new $formClass;
        $formNode->showInFragment($fragment);
        $this->add($fragment);
        Logger::info("Load $formClass to settings form.");
    }

}
