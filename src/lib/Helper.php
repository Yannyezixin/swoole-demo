<?php namespace ysd\lib;

define("LOG_UNKNOWN", 0);

define("LOG_INFO", 1);

define("LOG_WARNNING", 2);

define("LOG_DEBUG", 3);

define("LOG_ERROR", 4);

define("LOG_DANGGER", 5);

/**
 *  帮助类
 */
class Helper
{
    /**
     * 日志输出
     */
    public static function log($msg, $level = LOG_INFO)
    {
        $levels = [LOG_UNKNOWN, INFO, WARNNING, DEBUG, ERROR, DANGGER];

        if (!in_array($level, $levels)) {
            $level = LOG_UNKNOWN;
        }

        // 输出
        echo "[{$levels[$level]}] " . date("H:i:s") . self::getMicrotime() . " : {$msg} \n";
    }

    /**
     * 获取毫秒
     */
    public static function getMicrotime()
    {
        $digit = 3;

        list($usec, $sec) = explode(" ", microtime());
        $msec = round($usec * pow(10, $digit));

        return $msec;
    }
}
