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

use \DOMXPath;

interface AnalyzerInterface
{
    /**
     * Every analyzer has \DOMXPath and it's associated document available. If
     * something else than XPath is used, then the document can be retreived
     * from the XPath-object.
     *
     * The document contains only one record, since the amount of data is
     * massive and is processed a node by node.
     */
    public function analyze(DOMXPath $xpath);

    // For historical reasons
    public function output();
}

