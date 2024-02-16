<?php

namespace Devoted\MemoryDB\Utility;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class ColorfulConsoleLogger {
  public static function getLogger(): LoggerInterface
  {
    $logger = new Logger('memory-db');

    $handler = new StreamHandler('php://stdout');
    $handler->setFormatter(new ColoredLineFormatter());
    $logger->pushHandler($handler);

    return $logger;
  }
}