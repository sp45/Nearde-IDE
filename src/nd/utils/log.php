<?php
namespace nd\utils;

class log 
{
    public static function print(string $prefix, string $class, string $text)
    {
        echo $prefix . ' [' . str_replace('\\', '.', $class) . '] ' . $text . "\n";
    }
    
    public static function info(string $class, string $text)
    {
        self::print('info', $class, $text);
    }
    
    public static function warn(string $class, string $text)
    {
        self::print('warn', $class, $text);
    }
}