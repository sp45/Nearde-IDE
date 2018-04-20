<?php
namespace nd\forms;

use std, gui, framework, nd;


class SplashForm extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        /** @var UXForm $form */
        $form = $GLOBALS['ND']->getFormManger()->getForm("Main");
        $form->show();
        waitAsync(3000, function () use ($this, $form) {
            if ($form->visible)
            {
                $this->hide();
                $form->toFront();
            }
        });
    }

}
