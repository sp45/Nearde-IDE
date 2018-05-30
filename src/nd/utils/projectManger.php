<?php
namespace nd\utils;

use nd;
use gui;
use framework;

class projectManger 
{
    private $templates;
    
    private $gunters;
    
    private $menus;
    
    public function registerTemplate(String $name, $class)
    {
        if ($this->templates[$name]) return;
        log::info(get_class($this), "Register template: " . $name);
        $this->templates[$name] = $class;
    }
    
    public function getTemplate($name)
    {
        log::info(get_class($this), "Getting template: " . $name);
        return $this->templates[$name];
    }
    
    public function getAll()
    {
        log::info(get_class($this), "Getting all templates.");
        return $this->templates;
    }
    
    public function regiserGlobalGunter(string $name, UXImageView $img, callable $callable, $text = null)
    {
        if ($this->gunters[$name]) return;
        
        $this->gunters[$name] = [
            'name'     => $name,
            'image'    => $img,
            'callable' => $callable,
            'text'     => $text
        ];
    }
    
    public function getGlobalGunters()
    {
        return $this->gunters;
    } 
    
    public function regiserGlobalMenu(string $name, UXMenu $menu)
    {
        if ($this->menus[$name]) return;
        
        $this->menus[$name] = $menu;
    }
    
    public function getGlobalMenus()
    {
        return $this->menus;
    }
}