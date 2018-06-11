<?php
namespace nd\forms;

use framework;
use nd;
use php\gui\framework\AbstractForm;
use nd\modules\IDE;

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

        $theme = IDE::get()->getThemeManger()->get(IDE::get()->getConfig()['settings']['style']);
        echo 'Form manger add css -> ' . $theme->getCssFile() . "\n";
        $this->addStylesheet($theme->getCssFile());
        $this->centerOnScreen();
    }
}