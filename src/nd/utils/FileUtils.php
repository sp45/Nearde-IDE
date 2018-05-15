<?php
namespace nd\utils;

use nd;
use php\io\IOException;
use gui;
use framework;
use std;

class FileUtils 
{
    public static function delete(string $pathDir) : bool
    {
        if ($dir = new File($pathDir)->findFiles()) {
            foreach ($dir as $path)
                $list[] = $path->getPath();
            
            if (isset($list)) {
                foreach ($list as $file)
                    (fs::isDir($file)) ? self::delete($file): fs::delete($file);
            }
        }
        return fs::delete($pathDir);
    }
    
    public static function copy(string $fromDir, string $toDir) : bool
    {
        if (fs::exists($fromDir = fs::abs($fromDir)) && fs::exists($toDir = fs::abs($toDir))) {
            if ($dir = new File($fromDir)->findFiles()) {
                foreach ($dir as $path)
                    $list[] = $path->getPath();
                    
                if (isset($list)) {
                    foreach ($list as $file) {
                        $path = $toDir . '\\' . fs::name($file);
                        if (fs::isDir($file)) {
                            fs::makeDir($path);
                            self::copy($file, $path);
                        } else fs::copy($file, $path);
                    }
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function move(string $fromDir, string $toDir) : bool
    {
        return fs::move($fromDir, $toDir);
    }
    
    public static function createFile($path, $name, $content = null) : string 
    { 
        if ($name == null || !fs::valid($name) || !fs::nameNoExt($name)) 
        {
            IDE::dialog("Не верное имя файла.");
            return;
        }
        
        if (fs::isDir($path))
            $path .= "/" . $name;
        else 
            $path = fs::parent($path) . "/" . $name;
        try        
            Stream::putContents($path, $content);
        catch (IOException $e) 
            IDE::dialog("Не удалось создать файл.");
            
        return $path;
    }
}