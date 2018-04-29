<?php
namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log {
  private static $_logger;

  private static function getLogger() {
    if (!self::$_logger) {
      self::$_logger = new Logger('App Log');
    }

    return self::$_logger;
  }

  public static function logError($msg) {
    // Donde va a guardar los logs
    self::getLogger()->pushHandler(new StreamHandler('logs/application.log', Logger::ERROR));
    // Qué va a guardar
    self::getLogger()->addError($msg);
  }
  
  public static function logInfo($msg) {
    // Donde va a guardar los logs
    self::getLogger()->pushHandler(new StreamHandler('logs/application.log', Logger::INFO));
    // Qué va a guardar
    self::getLogger()->addInfo($msg);
  }
}

?>