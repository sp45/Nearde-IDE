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
        $this->formManger->registerForm("Plugins", PluginsForm::class);
        
        $dir = File::of("./plugins/");
        /** @var File $file */
        foreach ($dir->findFiles() as $file)
        {
            if (fs::isDir($file))
            {
                $name = fs::nameNoExt($file);
                $data = Json::fromFile($file->getAbsolutePath() . "/" . $name . ".json");
                Runtime::addJar($file->getAbsolutePath() . "/" . $name . ".jar");
                $this->pluginsManger->registerPlugin($name, $data['mainClass']);
            }
        }
        
        $this->formManger->getForm("Main")->show();
        
        Logger::info("init - done.");
        
        Logger::info("Starting plugins.");
        
        foreach ($this->pluginsManger->getAll() as $name => $pluginClass)
        {
            try {
                Logger::info("Starting: " . $name);
                $plugin = new $pluginClass();
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