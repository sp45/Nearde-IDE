<?php
namespace nd\formats\files;

use gui;
use nd;

class fileFormat 
{

    private $formats;
    private $templats;
    private $ext4lang;
    
    public function init()
    {
        $this->formats = [
            'css' => 'res://.data/img/css16.png',
            'jpg' => 'res://.data/img/jpg16.png',
            'png' => 'res://.data/img/png16.png',
            'css' => 'res://.data/img/css16.png',
            'jar' => 'res://.data/img/jar16.png',
            'zip' => 'res://.data/img/zip16.png',
            '7z'  => 'res://.data/img/7z16.png',
            'bat' => 'res://.data/img/bat16.png',
            'exe' => 'res://.data/img/exe16.png',
            'java' => 'res://.data/img/jar16.png',
            'ndproject' => 'res://.data/img/nd16.png',
        ];
        
        // more icons https://www.iconfinder.com/iconsets/fatcow
        
        $this->ext4lang = [
            'php'  => 'php',
            'json' => 'json',
            'ndproject' => 'json',
            'yml'  => 'yaml',
            'java' => 'java',
            'groovy' => 'groovy',
            'gradle' => 'groovy',
            'xml' => 'xml',
            'dnproject' => 'xml',
            'ini' => 'ini',
            'cfg' => 'ini',
            'conf' => 'ini',
            'html' => 'html',
            'css' => 'css',
            'js' => 'javascript',
        ];
    }
    
    public function getIcon(string $ext)
    {
        if (!$this->formats[$ext]) return IDE::ico("file.png");
        return IDE::image($this->formats[$ext]);
    }
    
    public function registerIcon(string $ext, string $ico)
    {
        if ($this->formats[$ext]) return;
        
        $this->formats[$ext] = $ico;
    }
    
    public function registerFileTemplate(UXMenuItem $item)
    {
        $this->templats[] = $item;
    }
    
    public function getFileTemplats($path)
    {
        $list = $this->templats;
        foreach ($list as $item)
            $item->userData = $path;
            
        return $list;
    }
    
    public function getLang4ext($ext)
    {
        if ($this->ext4lang[$ext])
        {
            return $this->ext4lang[$ext];
        } else return 'text';
    }
}