<?php
namespace nd;

use gui;
use facade\Json;
use php\desktop\Runtime;
use std;
use Error;
use framework;
use nd;

class ND 
{
    private $version = "2.0 alpha build 26"; // build is number commits on github
    private $name = "Nearde IDE";
    private $dev = false;
    private $configPath = "./config.json";
    
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
    
    /**
     * @var projectManger
     */
    private $projectManger;
    
    private $config;
    
    public function init()
    {
        echo '                                   ' . "\n";
        echo '   _  _____    _________  ___  ____' . "\n";
        echo '  / |/ / _ \  / ___/ __ \/ _ \/ __/' . "\n";
        echo ' /    / // / / /__/ /_/ / , _/ _/  ' . "\n";
        echo '/_/|_/____/  \___/\____/_/|_/___/  ' . "\n";
        echo '                                   ' . "\n";
        echo '                                   ' . "\n";
        
        // :3
        
        Logger::info("ND CORE starting init.");
        
        $libs = File::of("./libs");
        foreach ($libs->findFiles() as $lib)
        {
            if (fs::ext($lib) == "jar")
                Runtime::addJar($lib);
        }
        
        $this->formManger    = new formManger();
        $this->pluginsManger = new pluginsManger();
        $this->projectManger = new projectManger();
        $this->fileFormat    = new fileFormat();
        $this->fileFormat->init();
        
        $this->loadConfig();
        
        $this->formManger->registerForm("Main", MainForm::class);
        $this->formManger->registerForm("Project", ProjectForm::class);
        $this->formManger->registerForm("SandBox", SandBoxForm::class);
        $this->formManger->registerForm("Settings", SettingsForm::class);
        $this->formManger->registerForm("NewProject", NewProjectForm::class);
        $this->formManger->registerForm("OpenProject", OpenProjectForm::class);
        
        // froms for settings form :D
        $this->formManger->registerSettingForm("Основные", NeardeSettingsForm::class);
        $this->formManger->registerSettingForm("Редактор", CodeEditorSettingsForm::class);
        $this->formManger->registerSettingForm("Дополнения", PluginsForm::class);
        if ($this->isDev())
            $this->formManger->registerSettingForm("Песочница", SandBoxForm::class);
        
        $this->fileFormat->registerFileTemplate(NDTreeContextMenu::createItem("Пустой файл.", IDE::ico("file.png"), function ($item) {
            FileUtils::createFile($item->userData, UXDialog::input("Ввидите название нового файла."));
        }));
        
        $plugins = Json::fromFile("./plugins/plugins.json");
        foreach ($plugins as $plugin)
        {
            include fs::abs("./plugins/" . $plugin['dir'] . "/" . $plugin['file']);
        }
        
        Logger::info("Starting plugins.");
        
        foreach ($this->pluginsManger->getAll() as $name => $plugin)
        {
            Logger::info("Starting: " . $name);
            $plugin->onIDEStarting();
        }
        
        Logger::info("Plugins is started.");
        
        $this->formManger->getForm("Main")->show();
        
        Logger::info("ND CORE init - done.");
    }
    
    /**
     * @return formManger
     */
    public function getFormManger()
    {
        return $this->formManger;
    }
    
    /**
     * @return pluginsManger
     */
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
    
    /**
     * @return fileFormat
     */
    public function getFileFormat()
    {
        return $this->fileFormat;
    }
    
    /**
     * @return projectManger
     */
    public function getProjectManger()
    {
        return $this->projectManger;
    }
    
    private function loadConfig()
    {
        if (!fs::exists($this->configPath))
        {
            $this->loadDefaultConfig();
            return;
        }
        
        $this->config = Json::fromFile($this->configPath);
    }
    
    private function loadDefaultConfig()
    {
        $this->config = [
            "settings" => [
                "projectPath" => fs::abs("./projects/"),
                "editorStyle" => "chrome",
                "invisibles"  => false,
                "font_size"   => 14,
            ]
        ];
    }
    
    public function saveConfig()
    {
        Json::toFile($this->configPath, $this->config);
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function toConfig($newConfig)
    {
        if (UXDialog::confirm("Сохранить настройки ?"))
        {
            $this->config = $newConfig;
            $this->saveConfig();
        }
    }
}  