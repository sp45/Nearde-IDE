<?php

namespace nd\utils;


use nd\theme\AbstractTheme;

class ThemeManger
{
    /**
     * @var AbstractTheme[]
     */
    private $themes;

    /**
     * @param AbstractTheme $theme
     * @return $this
     */
    public function registerTheme(AbstractTheme $theme)
    {
        if (!$this->themes[$theme->getID()])
            $this->themes[$theme->getID()] = $theme;

        return $this;
    }

    /**
     * @param $id
     * @return AbstractTheme
     */
    public function get($id)
    {
        return $this->themes[$id];
    }

    /**
     * @param $name
     * @return AbstractTheme
     */
    public function getByName($name)
    {
        foreach ($this->themes as $theme)
        {
            if ($theme->getName() == $name) return $theme;
        }
    }

    /**
     * @return AbstractTheme[]
     */
    public function getAll()
    {
        return $this->themes;
    }
}