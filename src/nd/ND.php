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
    private $version = "2.0 beta build 40";
    private $buildVersion = "40";
    private $name = "Nearde IDE";
    private $dev = true;
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
    
    public function init($type = "window")
    {
        $this->configPath = fs::abs($this->getUserHome("config") . "/config.json");
        
        Logger::info("ND CORE starting init.");
        
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
        $this->formManger->registerForm("Update", UpdateForm::class);
        
        // dialog forms
        $this->formManger->registerForm("TreeDialog", TreeDialogForm::class);
        $this->formManger->registerForm("InputDialog", InputDialogForm::class);
        $this->formManger->registerForm("ConfirmDialog", ConfirmDialogForm::class);
        $this->formManger->registerForm("Dialog", DialogForm::class);
        $this->formManger->registerForm("ProgressDialog", ProgressDialogForm::class);
        $this->formManger->registerForm("GithubPluginParser", GithubPluginParserForm::class);
        
        if ($type == "console") 
        {
            $this->consoleInit();
            return;
        }
        
        // froms for settings form :D
        $this->formManger->registerSettingForm("Основные", NeardeSettingsForm::class);
        $this->formManger->registerSettingForm("Редактор", CodeEditorSettingsForm::class);
        $this->formManger->registerSettingForm("Дополнения", PluginsForm::class);
        
        $this->fileFormat->registerFileTemplate(NDTreeContextMenu::createItem("Пустой файл.", IDE::ico("file.png"), function ($item) {
            FileUtils::createFile($item->userData, IDE::inputDialog("Ввидите название нового файла."));
        }));
        
        $this->projectManger->registerTemplate("Empty", new EmptyProjectTemplate);
        $this->fileFormat->registerEditor(new ProjectEditor, "ndproject"); // register custom editor for ndproject format
        
        $plugins = Json::fromFile("./plugins/plugins.json");
        foreach ($plugins as $name => $data)
        {
            include fs::abs("./plugins/" . $data['dir'] . "/" . $data['file']);
            $this->pluginsManger->registerPlugin($name, new $data['class']);
            $this->pluginsManger->setOfflineToPlugin($name, $data['offline']);
        }
        
        Logger::info("Starting plugins.");
        
        foreach ($this->pluginsManger->getAll() as $name => $plugin)
        {
            Logger::info("Starting: " . $name);
            if (!$this->pluginsManger->getOfflineForPlugin($name))
            {
                $plugin->onIDEStarting();
            } else {
                Logger::info("Plugin $name is offline");
            }
        }
        
        Logger::info("Plugins is started.");
        $this->formManger->getForm("Main")->show();
        
        new updater($this->buildVersion)->checkUpdate();
        
        Logger::info("ND CORE init - done.");
    }
    
    public function consoleInit()
    {
        foreach ($GLOBALS['argv'] as $key => $val)
        {
            if ($val == "--install")
                IDE::installPlugin($GLOBALS['argv'][$key + 1], "console");
        }
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
                "style" => "light",
                "editor" => [
                    "style" => "chrome",
                    "invisibles"  => false,
                    "font_size"   => 15,
                ]
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
        if (IDE::confirmDialog("Сохранить настройки ?"))
        {
            $this->config = $newConfig;
            $this->saveConfig();
        }
    }
    
    public function getUserHome(string $dir): string
    {
        $home = System::getProperty('user.home');
        $result = fs::normalize("$home/.nd/$dir");
        if (!fs::isDir($result)) {
            if (!fs::makeDir($result)) {
                return null;
            }
        }
        return $result;
    }
}  