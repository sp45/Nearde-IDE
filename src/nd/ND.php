<?php
namespace nd;

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

    public function init()
    {
        Logger::info("Nearde starting init.");
        $this->formManger = new formManger();
        $this->fileFormat = new fileFormat();
        $this->fileFormat->init();
        
        $this->formManger->registerForm("Main", MainForm::class);
        $this->formManger->registerForm("Project", ProjectForm::class);
        $this->formManger->registerForm("SandBox", SandBoxForm::class);
        
        $this->formManger->getForm("Main")->show();
        
        Logger::info("init - done.");
    }
    
    public function getFormManger()
    {
        return $this->formManger;
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