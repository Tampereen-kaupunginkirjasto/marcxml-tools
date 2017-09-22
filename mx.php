<?php

/**
 * This file is part of MARCXML-toolset used to handle MARCXML-dataset of PIKI-
 * libraries.
 *
 * @author Miika Koskela <miika.koskela@pp3.inet.fi>
 * @copyright Tampereen kaupunginkirjasto - Pirkanmaan maakuntakirjasto
 * @license MIT-License, see LICENCE-file for more information
 */

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use PIKI\MARCXML\Event\RecordEvent;
use PIKI\MARCXML\Listener\KeywordListener;
use PIKI\MARCXML\Listener\NamespaceListener;
use PIKI\MARCXML\Xml\Record;
use PIKI\MARCXML\Iterator\XmlFilter;

require_once __DIR__ . "/vendor/autoload.php";

if($argc !== 2) {
    echo "Usage:\n php mx.php <input-dir>\n";
    exit;
}

$config = json_decode(file_get_contents(
    __DIR__ . "/config.json"
), true);

// Logger
$logger = new Logger("KEYWORDS");

$handler = (new StreamHandler(STDOUT))
    ->setFormatter(new LineFormatter(
        "[%datetime%] %channel%.%level_name%: %message%\n"
    ));
$logger->pushHandler($handler);

// Wiring up events and dispatcher
$dispatcher = new EventDispatcher();
$dispatcher->addListener(RecordEvent::NAME, [new KeywordListener($logger), "onRecord"]);

// Handling the data
$iterator = new XmlFilter(new DirectoryIterator($argv[1]));
foreach($iterator as $item) {

    $reader = new XMLReader;
    $reader->open($item->getPathname(), "UTF-8", LIBXML_NOBLANKS);

    $reader->read();
    $reader->read();

    do {

        if($reader->name !== "record") {
            break;
        }

        $record = (new Record($reader->readOuterXML()))
            ->registerNamespace($config["prefix"], $config["namespace"]);
        $dispatcher->dispatch(RecordEvent::NAME, new RecordEvent($record));

    } while($reader->next());

    $reader->close();
}
