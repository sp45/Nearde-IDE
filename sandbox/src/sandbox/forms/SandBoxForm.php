<?php

namespace sandbox\forms;

use php\gui\framework\AbstractForm;

class SandBoxForm extends AbstractForm
{
    /**
     * @param null $e
     * @event mySuperButton.action
     */
    public function doButtonAction($e = null)
    {
        alert('PHP + JSON = UXForm');
    }
}