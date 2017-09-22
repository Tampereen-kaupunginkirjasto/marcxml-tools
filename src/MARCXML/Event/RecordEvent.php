<?php

namespace PIKI\MARCXML\Event;

use Symfony\Component\EventDispatcher\Event;
use PIKI\MARCXML\Xml\Record;

/**
 * A record event
 *
 * Fired when there's new record ready.
 *
 * @author Miika Koskela
 * @license MIT
 */
class RecordEvent extends Event
{
    // Unique event name
    const NAME = "record.event";

    // @var Record
    protected $record;

    public function __construct(Record $record)
    {
        $this->record = $record;
    }

    public function getRecord() : Record
    {
        return $this->record;
    }
}
