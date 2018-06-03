<?php
namespace nd\utils;

use nd;
use gui;
use framework;
use php\framework\Logger;

class projectManger 
{
    private $templates;
    
    private $gunters;
    
    private $menus;
    
    public function registerTemplate(String $name, $class)
    {
        if ($this->templates[$name]) return;
        Logger::info("Register template: " . $name);
        $this->templates[$name] = $class;
    }
    
    public function getTemplate($name)
    {
        Logger::info("Getting template: " . $name);
        return $this->templates[$name];
    }
    
    public function getAll()
    {
        Logger::info("Getting all templates.");
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