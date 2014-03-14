<?php

/**
 * This file is part of MARCXML-toolset used to handle MARCXML-dataset of PIKI-
 * libraries.
 *
 * @author Miika Koskela <miika.koskela@pp3.inet.fi>
 * @copyright Tampereen kaupunginkirjasto - Pirkanmaan maakuntakirjasto
 * @licence MIT-License, see LICENCE-file for more information
 */

namespace PIKI\MARCXML;

use \InvalidArgumentException,
    \DOMXpath;

/**
 * This class is used to register and execute analyzers
 */
class Analytic
{
    /**
     * Analyzer collection
     *
     * @var array
     */
    private $analyzers = array();

    /**
     * Adds an array of anaylzers to analyzer collection by it's class name
     *
     * @param array $analyzers
     */
    public function registerAnalyzers(array $analyzers)
    {
        foreach($analyzers as $analyzer) {
            $name = get_class($analyzer);
            if(array_key_exists($name, $this->analyzers)) {
                throw new InvalidArgumentException(
                    "Analyzer with same name already exists."
                );
            }

            $this->analyzers[$name] = $analyzer;
        }
    }

    /**
     * Execute the analyzer on the document
     *
     * @param \DOMXpath $xpath
     */
    public function run(DOMXpath $xpath)
    {
        $analyzers = $this->analyzers;
        foreach($analyzers as $analyzer) {
            $analyzer->analyze($xpath);
        }
    }

    // 
    public function stat()
    {
        $analyzers = $this->analyzers;
        foreach($analyzers as $analyzer) {
            $analyzer->output();
        }
    }
}



