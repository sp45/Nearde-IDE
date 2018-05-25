<?php
namespace nd\forms;

use framework;
use nd;

abstract class AbstarctIDEForm extends AbstractForm
{
    public function show()
    {
        $this->addCustomStyle();
        parent::show();
    }
    
    public function showAndWait()
    {
        $this->addCustomStyle();
        parent::showAndWait();
    }
    
    private function addCustomStyle()
    {
        $this->clearStylesheets();
        $this->addStylesheet(".theme/". IDE::get()->getConfig()['settings']['style'] .".css");
    }
}