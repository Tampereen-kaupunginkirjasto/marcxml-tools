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
 * - Tag 653
 *
 */
class K653Analyzer implements AnalyzerInterface
{
    /**
     * Implements the Analyzer interface.
     *
     * It finds and stores main language codes (tag: 041, ind1: 0, subfield code: a)
     *
     * @param \DOMXPath xpath
     * @return
     */
    public function analyze(DOMXpath $xpath)
    {
        // Identify node. The node has its ID at controlfield, which has tag 001
        $controlfields = $xpath->query("//rc:controlfield[@tag = '001']");
        if($controlfields->length <= 0) {
            echo "Could not identify node. Skipping...\n";
            return;
        }

        $controlfield = $controlfields->item(0);
        $id = $controlfield->nodeValue;

        // All keywords in every datafield with tag 653 and subfield code a
        $nodes = $xpath->query("//rc:datafield[@tag = '653']/rc:subfield[@code = 'a']");
        foreach($nodes as $node) {
            $keyword = $node->nodeValue;
            echo "{$id}\t{$keyword}\n";
        }
    }

    public function output() {} // Not needed

}
