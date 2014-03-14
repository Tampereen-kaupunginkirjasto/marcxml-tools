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

use \PIKI\MARCXML\Analyzer\AnalyzerInterface;

/**
 * This analyzer will extract all publish years and classifications from nodes
 * that are located in Tampere region.
 *
 * Extracted data:
 *
 * - ID ($id)
 * - ISIL-code ($location_string)
 * - Classification ($classification)
 * - Publish year ($year)
 *
 * The output format is following:
 *
 * id<tab>isil<tab>classification<tab>publish year<newline>
 *
 * The output can then be piped to unix tee-command, for example, for later
 * analysis.
 *
 * Only data from main objects is collected. The sub-objects, that contain always
 * 773 field, are ignored. Also, if there's no 852$a-field that contains the ISIL
 * code of Tampere region libraries in some form, are ignored.
 *
 * The data collected:
 *
 * - ISIL-code from `852$a`, if it matches the word `tam`; the first occurence only
 * - Classification code `852$h` from the `852`-field matched above
 * - Node identity from controlfield `001`
 * - Publish year from controlfield `008`, from character places 07 to 10
 */
class FITamPublishYearAnalyzer implements AnalyzerInterface
{
    /**
     * Implements the Anaylzer interface. The XPath-instance must be already
     * configured with the document, the namespaces and their prefixes (rc used
     * here) and PHP functions for use in XPath.
     *
     * @param \DOMXpath $xpath
     */
    public function analyze(\DOMXpath $xpath)
    {
        $id = $classification = $year = '';

        // Detect sub-object and skip them; purpose is only analyze main objects
        // Return early, if it's not main object
        $subobjects = $xpath->query("//rc:datafield[@tag = '773']");
        if($subobjects->length > 0) {
            return;
        }

        // Is it located in Tampere...?
        $locations = $xpath->query(
            "//rc:datafield[@tag = '852' and php:functionString('preg_match', '/(tam)/i', rc:subfield[@code = 'a']) = 1][1]"
        );

        // ...If not, skip this node.
        if($locations->length < 1) {
            return;
        }

        $children = $locations->item(0)->childNodes;
        if($children->length  < 1) {
            echo "Child nodes not found. Skipping...\n";
            return;
        }

        $location_string = '';
        foreach($children as $child) {

            // These should be all subfields, but just in case, it's not
            if($child->tagName !== 'subfield') {
                continue;
            }

            if($child->getAttribute('code') === 'a') {
                $location_string = $child->nodeValue;
            }

            // Identify the node.
            $code = $child->getAttribute('code');

            // If it's $h, get the value and trim spaces
            if($code === 'h') {
                $classification = trim($child->nodeValue);
                break;
            }
        }

        // Node identity
        $idcontrols = $xpath->query("//rc:controlfield[@tag = '001']");
        if($idcontrols->length > 0) {
            $id = $idcontrols->item(0)->nodeValue;
        }

        // Publishing year
        $controlfields = $xpath->query("//rc:controlfield[@tag = '008']");
        if($controlfields->length > 0) {
            $year = substr($controlfields->item(0)->nodeValue, 7, 4);
        }

        // Output
        echo "{$id}\t{$location_string}\t{$classification}\t{$year}\n";
    }

    public function output()
    {
        //
    }

}
