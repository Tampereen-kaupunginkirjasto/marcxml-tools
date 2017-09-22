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
use PIKI\MARCXML\Event\RecordEvent;
use PIKI\MARCXML\Xml\Record;

require_once __DIR__ . "/vendor/autoload.php";

if($argc !== 2) {
    echo "Usage:\n php mx.php <input-dir>\n";
    //exit;
}

// Wiring up events and dispatcher
$dispatcher = new EventDispatcher();
$dispatcher->addListener(RecordEvent::NAME, function(Event $event) {
    $record = $event->getRecord();
    var_dump($record); exit;
});


// Handling the data
$iterator = new class(new \DirectoryIterator($argv[1])) extends FilterIterator {
    public function accept() : bool {
        $current = parent::current();
        if(preg_match("/\.xml$/i", $current))
            return true;
        return false;
    }
};

foreach($iterator as $item) {

    $reader = new \XMLReader;
    $reader->open($item->getPathname(), "UTF-8", LIBXML_NOBLANKS);
    $reader->read();
    $reader->read();

    do {

        $node = $reader->expand();
        if($node->nodeName === "records") {
            break;
        }

        $record = new Record($reader->readOuterXML());
        $dispatcher->dispatch(RecordEvent::NAME, new RecordEvent($record));

    } while($reader->next());

    $reader->close();
}
