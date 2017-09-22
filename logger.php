<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

// Logger, handler, formatter
$logger = new Logger("KEYWORDS");

$handler = (new StreamHandler(STDOUT))
    ->setFormatter(new LineFormatter(
        "[%datetime%] %channel%.%level_name%: %message%\n"
    ));
$logger->pushHandler($handler);

return $logger;
