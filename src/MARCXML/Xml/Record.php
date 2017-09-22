<?php

namespace PIKI\MARCXML\Xml;

use DOMDocument;
use DOMNodeList;
use DOMNode;
use DOMXpath;

class Record extends DOMDocument
{
    /**
     * An instance of DOMXPath
     *
     * @var DOMXPath
     */
    private $xpath;

    // Constructor
    public function __construct(string $xml)
    {
        $this->loadXML($xml);
        $this->xpath = new DOMXpath($this);
    }

    /**
     * Allow replacing DOMXpath.
     *
     * This just uses default DOMXPath without any configuration. But if user
     * wants to, for example, register namespaces and use different queries,
     * this is possible then.
     *
     * @param DOMXPath
     * @return Record
     */
    public function setXPath(DOMXpath $xpath) : self
    {
        $this->xpath = $xpath;
        return $this;
    }

    /**
     * Query as a wrapper for DOMXPath-member.
     *
     * See DOMXPath documentation for more.
     *
     * @param string $query
     * @param DOMNode $context
     * @param bool $registerNodeNS
     * @return DOMNodeList
     */
    public function query(string $query, DOMNode $context = null, bool $registerNodeNS = true) : DOMNodeList
    {
        return $this->xpath->query($query, $context, $registerNodeNS);
    }
}
