<?php

namespace PIKI\MARCXML\Listener;

use Symfony\Component\EventDispatcher\Event;
use Psr\Log\LoggerInterface;

class KeywordListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function onRecord(Event $event)
    {
        $keywords = [];

        $record = $event->getRecord();

        $datafields = $record->query("//{{prefix}}:datafield[@tag = '650']");
        foreach($datafields as $datafield) {

            // //subfield[@code = '2'] has the dictionary name, like YSA etc.
            $result = $record->query(
                "//{{prefix}}:subfield[@code = '2']"
            );

            // N/A if not available, obviously
            if($result->length < 1) {
                $dictionary = "N/A";
            } else {
                $dictionary = $result->item(0)->nodeValue;
            }

            // Keywords
            $result = $record->query(
                "{{prefix}}:subfield[@code = 'a' or @code = 'x']",
                $datafield, true
            );

            if($result->length > 0) {
                $keywords[$dictionary] = [];
                foreach($result as $subfield) {
                    $keywords[$dictionary][] = $subfield->nodeValue;
                }

                $keywords[$dictionary] = join(", ", $keywords[$dictionary]);
            }
        }

        if(count($keywords)> 1) {
            var_dump($keywords); exit;
        }

        $parts = [];
        foreach($keywords as $key => $words) {
            $parts[] = "{$key}: $words";
        }

        $message = join("; ", $parts);
        $this->logger->info("id: {$record->getIdentity()}; {$message}");
    }
}
