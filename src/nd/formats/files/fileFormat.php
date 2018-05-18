<?php
namespace nd\formats\files;

use std;
use gui;
use nd;

class fileFormat 
{

    private $formats;
    private $templats;
    private $ext4lang;
    
    private $editors;
    
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
            // цэ пу пус и цэ
            'c' => 'c_cpp',
            'cc' => 'c_cpp',
            'cpp' => 'c_cpp',
            'h' => 'c_cpp',
            'hpp' => 'c_cpp',
            // кэк :D
            'md' => 'markdown',
            'makefile' => 'makefile',
            'py' => 'python',
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
    
    public function getLang($path)
    {
        $path = strtolower($path);
        
        if ($this->ext4lang[fs::ext($path)])
        {
            return $this->ext4lang[fs::ext($path)];
        } else if ($this->ext4lang[fs::nameNoExt($path)])
        {
            return $this->ext4lang[fs::nameNoExt($path)];
        } 
        
        return 'text';
    }
    
    public function registerEditor(UXNode $editor, string $ext)
    {
        if ($this->editors[$ext]) return;
        $this->editors[$ext] = $editor;
    }
    
    public function getEditor(string $path) : UXNode
    {
        if ($this->editors[fs::ext($path)]) {
            $editor = clone $this->editors[fs::ext($path)];
            $editor->open($path);
            return $editor;
        }
        
        return $this->getCodeEditor($path);
    }
    
    public function getCodeEditor(string $path) : NDCode
    {
        return new NDCode($path, $this->getLang(fs::ext($path)));
    }
}