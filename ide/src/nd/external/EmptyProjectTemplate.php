<?php
namespace nd\external;

use nd;

use nd\projectTemplate;
use nd\modules\IDE;

class EmptyProjectTemplate extends projectTemplate
{
    public function getName()
    {
        return "Пустой проект.";
    }
    
    public function getIcon()
    {
        return IDE::ico("empty.png");
    }
    
    public function getDscription()
    {
        return "Пустой проект в котором нет ничего по умолчанию.";
    }
}