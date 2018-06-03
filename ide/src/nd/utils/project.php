<?php
namespace nd\utils;

use gui;
use nd;
use facade\Json;
use nd\projectTemplate;

class project 
{
    private $path;
    private $name;
    private $config;
    private $template;

    /**
     * project constructor.
     * @param string $path
     * @param string $name
     * @param ProjectTemplate $template
     */
    public function __construct(string $path, string $name, ProjectTemplate $template)
    {
        $this->path = $path;
        $this->name = $name;
        $this->template = $template;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function loadConfig(string $path)
    {
        $this->config = Json::fromFile($path);
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function getTemplate()
    {
        return $this->template;
    }
}