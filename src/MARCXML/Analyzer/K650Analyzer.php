<?php

/**
 * This file is part of MARCXML-toolset used to handle MARCXML-dataset of PIKI-
 * libraries.
 *
 * @author Miika Koskela <miika.koskela@pp3.inet.fi>
 * @copyright Tampereen kaupunginkirjasto - Pirkanmaan maakuntakirjasto
 * @licence MIT-License, see LICENCE-file for more information
 */

namespace PIKI\MARCXML\Analyzer;

use \DOMXPath,
    \PIKI\MARCXML\Analyzer\AnalyzerInterface;

/**
 * Analyzes keywords in MARCXML-record
 *
 * Field information:
 *
 * - Tag 650
 * - Code a
 *
 */
class K650Analyzer implements AnalyzerInterface
{
    private $keywords = array();

    /**
     * Implements the Analyzer interface.
     *
     * @param \DOMXPath xpath
     * @return
     */
    public function analyze(DOMXpath $xpath)
    {
        // Identify result node
        $controlfields = $xpath->query("//rc:controlfield[@tag = '001']");
        if($controlfields->length <= 0) {
            echo "Could not identify node. Skipping...\n";
            return;
        }

        $controlfield = $controlfields->item(0);
        $id = $controlfield->nodeValue;
        $dictionary = "";

        $datafields = $xpath->query("//rc:datafield[@tag = '650']");
        foreach($datafields as $datafield) {

            // Get dictionary name (e.g. Kaunokki, YSA etc.)
            $tag = $datafield->getAttribute("tag");
            $subfields = $datafield->getElementsByTagName("subfield");
            foreach($subfields as $subfield) {
                $code = $subfield->getAttribute("code");
                if($code !== "2") {
                    continue;
                }

                $dictionary = trim($subfield->nodeValue);

                // If dictionary is missing, then use n/a value
                if(empty($dictionary)) {
                    $dictionary = "n/a";
                }
            }

            // Get keywords on subfields a and x
            foreach($subfields as $subfield) {

                //
                $code = trim($subfield->getAttribute("code"));
                if($code !== "a" && $code !== "x") {
                    continue;
                }

                $keyword = $subfield->nodeValue;
                echo "{$id}\t{$tag}\${$code}\t{$keyword}\t{$dictionary}\n";
            }
        }
    }

    /**
     * Get count of all keywords
     *
     * @param
     * @return
     */
    public function getCount()
    {
        return count($this->keywords);
    }

    /**
     * Get all codes
     *
     * @param
     * @return
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    public function reset()
    {
        $this->keywords = array();
    }

    public function output()
    {
        // Not in use. echo "\n";
    }
}