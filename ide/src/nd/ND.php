<?php
namespace nd;

use gui;
use facade\Json;
use nd\external\editors\MarkdownEditor;
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
use nd\forms\SandBoxForm;
use nd\forms\SettingsForm;
use nd\forms\TreeDialogForm;
use nd\forms\UpdateForm;
use nd\theme\DarkTheme;
use nd\theme\LightTheme;
use nd\utils\ThemeManger;
use nd\utils\updater;
use php\desktop\Runtime;
use php\framework\Logger;
use php\gui\UXForm;
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
use php\lang\System;

class ND 
{
    private $version = "Alpha";
    private $buildVersion = "1";
    private $name = "Walltalk";
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

    /**
     * @var ThemeManger
     */
    private $themeManger;
    
    private $config;

    /**
     * @param string $type
     * @throws \Exception
     */
    public function init($type = "window", UXForm $splash = null)
    {
        $this->configPath = fs::abs($this->getUserHome("config") . "/config.json");

        $this->formManger    = new formManger();
        $this->pluginsManger = new pluginsManger();
        $this->projectManger = new projectManger();
        $this->fileFormat    = new fileFormat();
        $this->themeManger   = new ThemeManger();

        $this->fileFormat->init();
        $this->loadConfig();

        $this->themeManger->registerTheme(new LightTheme);
        $this->themeManger->registerTheme(new DarkTheme);

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
        
        $this->fileFormat->registerFileTemplate(NDTreeContextMenu::createItem("Empty", IDE::ico("file.png"), function ($item) {
            FileUtils::createFile($item->userData, IDE::inputDialog("Directory Name:"));
        }));
        
        $this->projectManger->registerTemplate("Empty", new EmptyProjectTemplate);
        $this->fileFormat->registerEditor(new MarkdownEditor([]), [
            'md', 'markdown'
        ]);
        
        $plugins = Json::fromFile("./plugins/plugins.json");
        foreach ($plugins as $name => $data)
        {
            $this->addToRuntime("./plugins/" . $data['dir']);

            $this->pluginsManger->registerPlugin($name, new $data['class']);
            $this->pluginsManger->setOfflineToPlugin($name, $data['offline']);
        }
        
        foreach ($this->pluginsManger->getAll() as $name => $plugin)
        {
            /** @var Plugin $plugin */
            if (!$this->pluginsManger->getOfflineForPlugin($name))
            {
                try {
                    $plugin->onIDEStarting();
                } catch (\Exception $e) {
                    Logger::error("Error starting plugin $name");
                    echo $e->getMessage();
                }
            }
        }
        (new updater($this->buildVersion))->checkUpdate();
        $this->formManger->getForm("Main")->show();
        if ($splash) $splash->hide();
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

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
                "style" => "dark",
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
        if (IDE::confirmDialog("Save settings?"))
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
     * @throws IOException
     */
    private function addToRuntime(string $dir)
    {
        echo 'Include dir to runtime -> ' . fs::abs($dir) . "\n";
        $file = new File(fs::abs($dir));
        $files = $file->findFiles();
        foreach ($files as $file)
        {
            if (fs::isDir($file)) $this->addToRuntime($file);

            if (fs::ext($file) == 'php')
            {
                echo 'Include php file -> ' . $file . "\n";

                try {
                    include (string) $file;
                } catch (Error $exception) {
                    Logger::error('Error including file ' . $file->getAbsolutePath());
                    Logger::error('Trace : ' . $exception->getTraceAsString());
                    Logger::error('Message : ' . $exception->getMessage());
                }
            }

            if (fs::ext($file) == 'jar')
            {
                echo 'Include jar file -> ' . $file . "\n";

                try {
                    Runtime::addJar($file);
                } catch (IOException $exception) {
                    Logger::error('Error including jar ' . $file->getAbsolutePath());
                    Logger::error('Trace : ' . $exception->getTraceAsString());
                    Logger::error('Message : ' . $exception->getMessage());
                }
            }
        }


    }

    /**
     * @return ThemeManger
     */
    public function getThemeManger(): ThemeManger
    {
        return $this->themeManger;
    }

    /**
     * @return string
     */
    public function getBuildVersion(): string
    {
        return $this->buildVersion;
    }
}  
