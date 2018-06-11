<?php

namespace nd\theme;


class DarkTheme extends AbstractTheme
{

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'Тёмная';
    }

    /**
     * @return string
     */
    public function getID() : string
    {
        return 'dark';
    }

    /**
     * @return string
     */
    public function getAuthor() : string
    {
        return 'Nearde';
    }

    /**
     * @return string
     */
    public function getCssFile() : string
    {
        return '.theme/dark.css';
    }
}