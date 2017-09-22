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

// Configuration contains all the config that is used during the app runtime.
// Currently, there's only namespace and namespace prefix in config.
$config = json_decode(file_get_contents(
    __DIR__ . "/config.json"
), true);

// Don't change the order. Events needs logging. But you can wire up more and/or
// different loggers. Everything that Monolog offers. KeywordListener accepts
// Psr\Log\LoggerInterface as constructor argument, so anything that implements
// that, can be used.
$logger = include_once "logger.php";
$events = include "events.php";

// Handling files. XmlFilter filters out all but files ending in .xml. You can
// find the code for this filter in `src/MARCXML/Iterator` since it extends PHPs
// FilterIterator.
$iterator = new XmlFilter(new DirectoryIterator($argv[1]));
foreach($iterator as $item) {

    $reader = new XMLReader;

    // Handling data
    $reader->open($item->getPathname(), "UTF-8", LIBXML_NOBLANKS);

    // Advance to first record-tag. Calls to read advances to the next tag in
    // the document. The data from PIKI-libraries has structure as follows:
    // <records>
    //   <record>
    //     <recordData>
    //       <record>
    //         ... and from here starts actual record data
    $reader->read();
    $reader->read();

    do {

        if($reader->name !== "record") {
            break;
        }

        // Here, new Record-instance is created. The Record-class is a subclass
        // of DOMDocument and it also wraps DOMXpath for easy querying. Because
        // PIKI-data uses namespaces, those need to be registered before using
        // the Record-instance.
        $record = (new Record($reader->readOuterXML()))
            ->registerNamespace($config["prefix"], $config["namespace"]);

        // Dispatching a record.event. All registered listeners gets the event
        // instance (a RecordEvent-instance) which has acecss to the Record
        // itself.
        $dispatcher->dispatch(RecordEvent::NAME, new RecordEvent($record));

    } while($reader->next());

    // Closing  the reader before starting loop all over again with new file in
    // the set of files.
    $reader->close();
}
