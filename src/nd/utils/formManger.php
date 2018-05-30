<?php
namespace nd\utils;

use nd;
use framework;

class formManger 
{
    private $forms;
    private $settingForms;
    
    public function registerForm(String $name, $class)
    {
        if ($this->forms[$name]) return;
        log::info(get_class($this), "Register form: " . $name);
        $this->forms[$name] = $class;
    }
    
    public function getForm($name)
    {
        log::info(get_class($this), "Getting form: " . $name);
        return new $this->forms[$name];
    }
    
    public function registerSettingForm(String $name, $class)
    {
        if ($this->settingForms[$name]) return;
        log::info(get_class($this), "Register setting form: " . $name);
        $this->settingForms[$name] = $class;
    }
    
    public function getSettingForm($name)
    {
        log::info(get_class($this), "Getting setting form: " . $name);
        return new $this->settingForms[$name];
    }
    
    public function getAllSettingsForm()
    {
        return $this->settingForms;
    }
}