<?php
namespace nd\external;

use nd;

class EmptyProjectTemplate extends ProjectTemplate
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