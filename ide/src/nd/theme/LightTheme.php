<?php

namespace nd\theme;


class LightTheme extends AbstractTheme
{

    public function getName() : string
    {
        return 'Светлая';
    }

    public function getID() : string
    {
        return 'light';
    }

    public function getCssFile() : string
    {
        return '.theme/light.css';
    }

    public function getAuthor() : string
    {
        return 'Nearde';
    }
}