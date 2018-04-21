<?php
namespace nd;

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
        $this->formManger->registerForm("Plugins", PluginsForm::class);
        
        $this->pluginsManger->registerPlugin("jphp", new jphpPlugin());
        
        $this->formManger->getForm("Main")->show();
        
        Logger::info("init - done.");
        
        Logger::info("Starting plugins.");
        
        foreach ($this->pluginsManger->getAll() as $name => $plugin)
        {
            Logger::info("Starting: " . $name);
            try {
                $plugin->onIDEStarting();
            } catch (Error $e) {
                Logger::warn("Error starting {$name} plugin.");
            }
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