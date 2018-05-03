<?php
namespace nd\utils;

use nd;
use facade\Json;

class project 
{
    private $path;
    private $name;
    private $config;
    private $template;
    
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
    
    public function getTemplate()
    {
        return get_class($this->template);
    }
}