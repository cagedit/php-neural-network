<?php
namespace Logging;

class Logger
{
    const RESET = "\e[0m";
    const GREEN = "\e[0;32m";
    const RED   = "\e[0;31m";
    const BLUE  = "\e[0;34m";

    /**
     * Default level
     * @var int
     */
    private static $level = 4;

    public static function setLevel(int $level)
    {
        self::$level = $level;
    }

    /**
     * Log varying level or errors
     * @param string $message
     * @param int $level
     */
    public static function log(string $message, int $level)
    {
        if (self::$level >= $level) {
            $prefix = date('Y-m-d H:i:s');

            switch ($level) {
                case LogLevelEnum::ERROR:
                    $color = self::RED;
                    $message = "{$color}{$prefix}: \033[0m{$message}";
                    break;
                case LogLevelEnum::ALL:
                default:
                    $color = self::GREEN;
                    $message = "{$color}{$prefix}: \033[0m{$message}";
                    break;

            }
            echo $message . PHP_EOL;
        }
    }

    /**
     * Shorthand to log an error.
     * @param string $string
     */
    public static function error(string $string)
    {
        self::log($string, LogLevelEnum::ERROR);
    }

    /**
     * Shorthand to log a debug message.
     * @param string $string
     */
    public static function debug(string $string)
    {
        self::log($string, LogLevelEnum::DEBUG);
    }

    /**
     * Shorthand to log an all level message.
     * @param string $string
     */
    public static function all(string $string)
    {
        self::log($string, LogLevelEnum::ALL);
    }


}