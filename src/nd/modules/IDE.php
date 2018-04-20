<?php
namespace nd\modules;

use std, gui, framework, nd;


class IDE extends AbstractModule
{
    public static function ico($name)
    {
        return new UXImageView(new UXImage("res://.data/img/" . $name));
    }
}