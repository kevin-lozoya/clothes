<?php
namespace App\Modules;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Log {
  private static $_logger;
  private static $_formatter;

  private static function getLogger() {
    if (!self::$_logger) {
      self::$_logger = new Logger('');
    }

    return self::$_logger;
  }

  private static function getFormatter() {
    if (!self::$_formatter) {
      // the default date format is "Y-m-d H:i:s"
      $dateFormat = "d-m-Y H:i:s";
      // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
      $output = "%datetime%|%level_name%|%message%|%context%|%extra%\n";
      // finally, create a formatter
      self::$_formatter = new LineFormatter($output, $dateFormat);
    }

    return self::$_formatter;
  }

  public static function logError($msg) {
    // Donde va a guardar los logs
    $stream = new StreamHandler('logs/application.log', Logger::ERROR);
    $stream->setFormatter(self::getFormatter());
    self::getLogger()->pushHandler($stream);
    // Qué va a guardar
    self::getLogger()->error($msg);
  }
  
  public static function logInfo($msg) {
    // Donde va a guardar los logs
    $stream = new StreamHandler('logs/application.log', Logger::INFO);
    $stream->setFormatter(self::getFormatter());
    self::getLogger()->pushHandler($stream);
    // Qué va a guardar
    self::getLogger()->info($msg);
  }
}

?>