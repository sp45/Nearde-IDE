<?php
namespace nd\utils;

use nd;
use framework;
use php\framework\Logger;

class formManger 
{
    private $forms;
    private $settingForms;

    /**
     * @param $name
     * @param $class
     */
    public function registerForm($name, $class)
    {
        if ($this->forms[$name]) return;
        Logger::info("Register form: " . $name);
        $this->forms[$name] = $class;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getForm($name)
    {
        Logger::info("Getting form: " . $name);
        if (!$this->forms[$name]) throw new \Exception('Form not found');
        return new $this->forms[$name];
    }

    /**
     * @param $name
     * @param $class
     */
    public function registerSettingForm($name, $class)
    {
        if ($this->settingForms[$name]) return;
        Logger::info("Register setting form: " . $name);
        $this->settingForms[$name] = $class;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getSettingForm($name)
    {
        Logger::info("Getting setting form: " . $name);
        return new $this->settingForms[$name];
    }

    /**
     * @return mixed
     */
    public function getAllSettingsForm()
    {
        return $this->settingForms;
    }
}