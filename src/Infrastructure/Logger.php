<?php

namespace App\Infrastructure;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Logger
{
    private static ?MonologLogger $logger = null;

    public static function getLogger(): MonologLogger
    {
        if (self::$logger === null) {
            self::$logger = new MonologLogger('app_logger');

            $logFile = __DIR__ . '/../../logs/app.log';
            
            $streamHandler = new StreamHandler($logFile, Level::Debug);
            self::$logger->pushHandler($streamHandler);
        }

        return self::$logger;
    }
}
