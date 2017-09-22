<?php

/**
 * This file is part of MARCXML-toolset used to handle MARCXML-dataset of PIKI-
 * libraries.
 *
 * @author Miika Koskela <miika.koskela@pp3.inet.fi>
 * @copyright Tampereen kaupunginkirjasto - Pirkanmaan maakuntakirjasto
 * @license MIT-License, see LICENCE-file for more information
 */

use PIKI\MARCXML\Xml\Record;
use PIKI\MARCXML\Iterator\XmlFilter;
use PIKI\MARCXML\Event\RecordEvent;

require_once __DIR__ . "/vendor/autoload.php";

// This script requires input directory as a command line -argument.
if($argc !== 2) {
    echo "Usage:\n php mx.php <input-dir>\n";
    exit;
}

// Read configuration
$config = json_decode(file_get_contents(
    __DIR__ . "/config.json"
), true);

$logger = include_once "logger.php";
$events = include "events.php";

// Handling files
$iterator = new XmlFilter(new DirectoryIterator($argv[1]));
foreach($iterator as $item) {

    $reader = new XMLReader;

    // Handling data
    $reader->open($item->getPathname(), "UTF-8", LIBXML_NOBLANKS);

    // Advance to first record-tag
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
