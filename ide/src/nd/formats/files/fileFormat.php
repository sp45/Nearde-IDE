<?php
namespace nd\formats\files;

use php\gui\UXMenuItem;
use php\gui\UXNode;
use php\lib\fs;
use std;
use gui;
use nd;

use nd\modules\IDE;
use nd\ui\NDCode;

class fileFormat 
{

    private $formats;
    private $templates;
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
            'c' => 'c_cpp',
            'cc' => 'c_cpp',
            'cpp' => 'c_cpp',
            'h' => 'c_cpp',
            'hpp' => 'c_cpp',
            'md' => 'markdown',
            'py' => 'python',
        ];
    }

    /**
     * @param string $ext
     * @return \php\gui\UXImageView
     */
    public function getIcon(string $ext)
    {
        if (!$this->formats[$ext]) return IDE::ico("file.png");
        return IDE::image($this->formats[$ext]);
    }

    /**
     * @param string $ext
     * @param string $ico
     */
    public function registerIcon(string $ext, string $ico)
    {
        $this->formats[$ext] = $ico;
    }

    /**
     * @param UXMenuItem $item
     */
    public function registerFileTemplate(UXMenuItem $item)
    {
        $this->templates[] = $item;
    }

    /**
     * @param $ext
     * @param $lang
     */
    public function registerLang4ext($ext, $lang)
    {
        $this->ext4lang[$ext] = $lang;  // fix bug
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getFileTemplats($path)
    {
        $list = $this->templates;
        foreach ($list as $item)
            $item->userData = $path;
            
        return $list;
    }

    /**
     * @param $path
     * @return string
     */
    public function getLang($path)
    {
        $path = strtolower($path);
        
        if ($this->ext4lang[fs::ext($path)])
            return $this->ext4lang[fs::ext($path)];
        else if ($this->ext4lang[fs::nameNoExt($path)])
            return $this->ext4lang[fs::nameNoExt($path)];
        else return 'text';
    }


    /**
     * @param UXNode $editor
     * @param $ext
     */
    public function registerEditor(UXNode $editor, $ext)
    {
        if (is_array($ext))
        {
            foreach ($ext as $extension)
                $this->registerEditor($editor, $extension);

            return;
        }

        if (is_string($ext))
        {
            if ($this->editors[$ext]) return;
            $this->editors[$ext] = $editor;
        }
    }

    /**
     * @param string $path
     * @return UXNode
     */
    public function getEditor(string $path)
    {
        if ($this->editors[fs::ext($path)]) {
            $obj = get_class($this->editors[fs::ext($path)]);
            $editor = new $obj;
            $editor->open($path);
            return $editor;
        }
        
        return $this->getCodeEditor($path);
    }

    /**
     * @param string $path
     * @return NDCode
     */
    public function getCodeEditor(string $path) : NDCode
    {
        return new NDCode($path, $this->getLang(fs::ext($path)));
    }
}