<?php

use PIKI\MARCXML\Event\RecordEvent;
use PIKI\MARCXML\Listener\KeywordListener;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

// Wire up events and event dispatcher here
$dispatcher = new EventDispatcher();

$dispatcher->addListener(RecordEvent::NAME, [new KeywordListener($logger), "onRecord"]);
