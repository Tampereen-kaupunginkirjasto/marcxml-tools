<?php

namespace PIKI\MARCXML\Xml;

use DOMDocument;
use DOMXpath;

class Record extends DOMDocument
{
    private $xpath;

    public function __construct(string $xml)
    {
        $this->loadXML($xml);
        $this->xpath = new DOMXpath($this);
    }

    public function registerNamespace(string $namespace)
    {
        # code...
    }

    public function query(string $query) : DOMNodeList
    {
        return $this->xpath($query);
    }
}
