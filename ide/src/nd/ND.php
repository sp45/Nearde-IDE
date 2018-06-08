<?php
namespace nd;

use gui;
use facade\Json;
use nd\forms\CodeEditorSettingsForm;
use nd\forms\ConfirmDialogForm;
use nd\forms\DialogForm;
use nd\forms\GithubPluginParserForm;
use nd\forms\InputDialogForm;
use nd\forms\MainForm;
use nd\forms\NeardeSettingsForm;
use nd\forms\NewProjectForm;
use nd\forms\PluginsForm;
use nd\forms\ProgressDialogForm;
use nd\forms\ProjectForm;
use nd\forms\SettingsForm;
use nd\forms\TreeDialogForm;
use nd\forms\UpdateForm;
use nd\utils\updater;
use php\desktop\Runtime;
use php\framework\Logger;
use php\io\File;
use php\io\IOException;
use std;
use Error;
use framework;

use nd\utils\formManger;
use nd\utils\pluginsManger;
use nd\utils\projectManger;
use nd\formats\files\fileFormat;
use nd\utils\FileUtils;
use php\lib\fs;

use nd\modules\IDE;

use nd\ui\NDTreeContextMenu;
use nd\external\EmptyProjectTemplate;
use nd\external\ProjectEditor;
use php\lang\System;

class ND 
{
    private $version = "2.0 beta build 43";
    private $buildVersion = "43";
    private $name = "Nearde IDE";
    private $dev = true;
    private $configPath;
    
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

    /**
     * @param string $type
     * @throws \Exception
     */
    public function init($type = "window")
    {
        $this->configPath = fs::abs($this->getUserHome("config") . "/config.json");

        $this->formManger    = new formManger();
        $this->pluginsManger = new pluginsManger();
        $this->projectManger = new projectManger();
        $this->fileFormat    = new fileFormat();

        $this->fileFormat->init();
        $this->loadConfig();
        
        $this->formManger->registerForm("Main", MainForm::class);
        $this->formManger->registerForm("Project", ProjectForm::class);
        $this->formManger->registerForm("Settings", SettingsForm::class);
        $this->formManger->registerForm("Update", UpdateForm::class);
        $this->formManger->registerForm("NewProject", NewProjectForm::class);
        $this->formManger->registerForm("GithubPluginParser", GithubPluginParserForm::class);

        $this->formManger->registerForm("TreeDialog", TreeDialogForm::class);
        $this->formManger->registerForm("InputDialog", InputDialogForm::class);
        $this->formManger->registerForm("ConfirmDialog", ConfirmDialogForm::class);
        $this->formManger->registerForm("Dialog", DialogForm::class);
        $this->formManger->registerForm("ProgressDialog", ProgressDialogForm::class);

        $this->formManger->registerSettingForm("Основные", NeardeSettingsForm::class);
        $this->formManger->registerSettingForm("Редактор", CodeEditorSettingsForm::class);
        $this->formManger->registerSettingForm("Дополнения", PluginsForm::class);
        
        if ($type == "console") 
        {
            $this->consoleInit();
            return;
        }
        
        $this->fileFormat->registerFileTemplate(NDTreeContextMenu::createItem("Пустой файл.", IDE::ico("file.png"), function ($item) {
            FileUtils::createFile($item->userData, IDE::inputDialog("Ввидите название нового файла."));
        }));
        
        $this->projectManger->registerTemplate("Empty", new EmptyProjectTemplate);
        
        $plugins = Json::fromFile("./plugins/plugins.json");
        foreach ($plugins as $name => $data)
        {
            $this->addToRuntime($data['dir']);

            $this->pluginsManger->registerPlugin($name, new $data['class']);
            $this->pluginsManger->setOfflineToPlugin($name, $data['offline']);
        }
        
        foreach ($this->pluginsManger->getAll() as $name => $plugin)
        {
            if (!$this->pluginsManger->getOfflineForPlugin($name))
            {
                try {
                    $plugin->onIDEStarting();
                } catch (Error $e) {
                    Logger::error("Error starting plugin $name");
                }
            } else {
            }
        }

        $this->formManger->getForm("Main")->show();

        (new updater($this->buildVersion))->checkUpdate();
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
                    "invisible"  => false,
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

    /**
     * @param string $dir
     */
    private function addToRuntime(string $dir)
    {
        $file = File::of($dir);

        foreach ($file->findFiles() as $file)
        {
            if (fs::isDir($file)) $this->addToRuntime($file);

            if (fs::ext($file) == 'php')
            {
                try {
                    include_once (string) $file;
                } catch (Error $exception) {
                    Logger::error('Error include file ' . $file->getAbsolutePath());
                    Logger::error('Trace : ' . $exception->getTraceAsString());
                    Logger::error('Message : ' . $exception->getMessage());
                }
            }

            if (fs::ext($file) == 'jar')
            {
                try {
                    Runtime::addJar($file);
                } catch (IOException $exception) {
                    Logger::error('Error include jar ' . $file->getAbsolutePath());
                    Logger::error('Trace : ' . $exception->getTraceAsString());
                    Logger::error('Message : ' . $exception->getMessage());
                }
            }
        }


    }
}  
