<?php
namespace nd;

abstract class Plugin 
{
    abstract public function getName();
    abstract public function getIcon();
    abstract public function getDscription();
    
    public function __construct()
    {
        call_user_func("onIDEStarting", null);
    }
}