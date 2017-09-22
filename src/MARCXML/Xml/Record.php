<?php

namespace PIKI\MARCXML\Xml;

use DOMDocument;
use DOMNodeList;
use DOMNode;
use DOMXpath;

/**
 * Record.
 *
 * @author Miika Koskela
 * @license MIT
 */
class Record extends DOMDocument
{
    /**
     * An instance of DOMXPath
     *
     * @var DOMXPath
     */
    private $xpath;

    /**
     * Prefix for namespace.
     *
     * Cannot use $prefix since DOMDocument has it already (?).
     *
     * @var string
     */
    private $nsPrefix;

    /**
     * Namespace URI
     *
     * @var string
     */
    private $namespace;

    /**
     * Constructor.
     *
     * @param string $xml An XML-formatted string
     */
    public function __construct(string $xml)
    {
        $this->loadXML($xml);
        $this->xpath = new DOMXpath($this);
    }

    /**
     * Registers namespace and prefix
     *
     * @param string $prefix
     * @param string $namespace A namespace URI
     * @return self
     */
    public function registerNamespace(string $prefix, string $namespace) : self
    {
        $this->nsPrefix = $prefix;
        $this->namespace = $namespace;
        $this->xpath->registerNamespace($this->nsPrefix, $this->namespace);
        return $this;
    }

    /**
     * Allow replacing DOMXpath.
     *
     * This just uses default DOMXPath without any configuration. But if user
     * wants to, for example, register namespaces and use different queries,
     * this is possible then.
     *
     * @param DOMXPath
     * @return self
     */
    public function setXPath(DOMXpath $xpath) : self
    {
        $this->xpath = $xpath;
        return $this;
    }

    /**
     * Identify a MARCXML-record.
     *
     * In MARCXML, controlfield with tag 001 contains the ISBN (?) of the entry.
     * This is used to identify each record.
     *
     * If identity cannot be found, then return string "N/A" instead.
     *
     * @param
     * @return string
     */
    public function getIdentity() : string
    {
        $result = $this->query("//{{prefix}}:controlfield[@tag = '001']");
        return $result->length > 0 ? $result->item(0)->nodeValue : "N/A";
    }

    /**
     * Query as a wrapper for DOMXPath-member.
     *
     * See DOMXPath documentation for more.
     *
     * This method also replaces {{prefix}}-placeholders from query string with
     * the $this->nsPrefix so that namespaced records can be queried too.
     *
     * @param string $query
     * @param DOMNode $context
     * @param bool $registerNodeNS
     * @return DOMNodeList
     */
    public function query(string $query, DOMNode $context = null, bool $registerNodeNS = true) : DOMNodeList
    {
        return $this->xpath->query(
            str_replace("{{prefix}}", $this->nsPrefix, $query),
            $context,
            $registerNodeNS
        );
    }
}
