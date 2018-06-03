<?php
namespace php\framework;

use php\time\Time;

/**
 * Class Logger
 * @package php\framework
 * @packages framework
 */
class Logger
{
    public static function log($level, $message)
    {
        echo strtolower($level) . ' (' . Time::now()->toString('HH:mm:ss') . ') ' . $message . "\n";
    }
    public static function info($message)
    {
        self::log('info', $message);
    }
    public static function debug($message)
    {
        self::log('debug', $message);
    }
    public static function warn($message)
    {
        self::log('warn', $message);
    }
    public static function error($message)
    {
        self::log('error', $message);
    }
    public static function arrowLog(string $message, int $level = 0, $cleanChars = 0)
    {
        for ($i=0; $i < $cleanChars; $i++) {
            echo ' ';
        }

        for ($i=0; $i < $level; $i++) {
            echo '-';
        }
        echo '> ' . $message . "\n";
    }
}