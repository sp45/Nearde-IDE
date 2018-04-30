<?php
namespace nd\utils;

use framework;

class formManger 
{
    private $forms;
    private $settingForms;
    
    public function registerForm(String $name, $class)
    {
        if ($this->forms[$name]) return;
        Logger::info("Register form: " . $name);
        $this->forms[$name] = $class;
    }
    
    public function getForm($name)
    {
        Logger::info("Getting form: " . $name);
        return new $this->forms[$name];
    }
    
    public function registerSettingForm(String $name, $class)
    {
        if ($this->settingForms[$name]) return;
        Logger::info("Register setting form: " . $name);
        $this->settingForms[$name] = $class;
    }
    
    public function getSettingForm($name)
    {
        Logger::info("Getting setting form: " . $name);
        return new $this->settingForms[$name];
    }
    
    public function getAllSettingsForm()
    {
        return $this->settingForms;
    }
}