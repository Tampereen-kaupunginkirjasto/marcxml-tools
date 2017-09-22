<?php

namespace PIKI\MARCXML\Event;

use Symfony\Component\EventDispatcher\Event;
use PIKI\MARCXML\Xml\Record;

class RecordEvent extends Event
{
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
