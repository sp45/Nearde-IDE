<?php
namespace nd;

use framework;
use nd;

class ND 
{
    private $version = "2.0 alpha";
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
        Logger::info("Nearde starting init. \n");
        $this->formManger = new formManger();
        $this->fileFormat = new fileFormat();
        $this->fileFormat->init();
        
        $this->formManger->registerForm("Main", MainForm::class);
        $this->formManger->registerForm("Splash", SplashForm::class);
        $this->formManger->registerForm("Test", TestForm::class); // nearde testing form
        
        $this->formManger->getForm("Splash")->show();
        
        Logger::info("init - done. \n");
    }
    
    public function getFormManger()
    {
        return $this->formManger;
    }
    
    public function getVersion()
    {
        return $this->version;
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