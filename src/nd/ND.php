<?php
namespace nd;

use facade\Json;
use php\desktop\Runtime;
use std;
use Error;
use framework;
use nd;

class ND 
{
    private $version = "2.0 alpha";
    private $name = "Nearde IDE";
    private $dev = true;
    
    /**
     * @var formManger
     */
    private $formManger;
    
    /**
     * @var fileFormat
     */
    private $fileFormat;
    
    /**
     * @var pluginsManger
     */
    private $pluginsManger;
    
    public function init()
    {
        Logger::info("Nearde starting init.");
        $this->formManger    = new formManger();
        $this->pluginsManger = new pluginsManger();
        $this->fileFormat    = new fileFormat();
        $this->fileFormat->init();
        
        $this->formManger->registerForm("Main", MainForm::class);
        $this->formManger->registerForm("Project", ProjectForm::class);
        $this->formManger->registerForm("SandBox", SandBoxForm::class);
        $this->formManger->registerForm("Settings", SettingsForm::class);
        
        // froms for settings form :D
        $this->formManger->registerSettingForm("Основные", NeardeSettingsForm::class);
        $this->formManger->registerSettingForm("Дополнейния", PluginsForm::class);
        
        $plugins = Json::fromFile("./plugins/plugins.json");
        foreach ($plugins as $plugin)
        {
            include fs::abs("./plugins/" . $plugin['dir'] . "/" . $plugin['file']);
        }
        
        $this->formManger->getForm("Main")->show();
        
        Logger::info("init - done.");
        
        Logger::info("Starting plugins.");
        
        foreach ($this->pluginsManger->getAll() as $name => $plugin)
        {
            Logger::info("Starting: " . $name);
            $plugin->onIDEStarting();
        }
        
        Logger::info("Plugins is started.");
    }
    
    public function getFormManger()
    {
        return $this->formManger;
    }
    
    public function getPluginsManger()
    {
        return $this->pluginsManger;
    }
    
    public function getVersion()
    {
        return $this->version;
    }
    public function getName()
    {
        return $this->name;
    }
    
    public function isDev()
    {
        return $this->dev;
    }
    
    public function getFileFormat()
    {
        return $this->fileFormat;
    }
}