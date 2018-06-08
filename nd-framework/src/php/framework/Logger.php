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
    private static $visible = true;

    /**
     * @param $level
     * @param $message
     */
    public static function log($level, $message)
    {
        if (self::$visible)
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
        self::log('warning', $message);
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

    /**
     * @param bool $visible
     */
    public static function setVisible(bool $visible)
    {
        self::$visible = $visible;
    }
}