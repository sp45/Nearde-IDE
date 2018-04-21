<?php
namespace nd\modules;

use std, gui, framework, nd;


class IDE extends AbstractModule
{
    public static function ico($name)
    {
        return new UXImageView(new UXImage("res://.data/img/" . $name));
    }
    
    public static function get()
    {
        return $GLOBALS['ND'];
    }
}