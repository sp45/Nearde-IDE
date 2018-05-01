<?php
namespace nd\utils;

use facade\Json;

class project 
{
    private $path;
    private $name;
    private $config;
    
    public function __construct(string $path, string $name)
    {
        $this->path = $path;
        $this->name = $name;
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
}