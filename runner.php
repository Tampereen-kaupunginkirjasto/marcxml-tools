<?php

/**
 * This file is part of MARCXML-toolset used to handle MARCXML-dataset of PIKI-
 * libraries.
 *
 * @author Miika Koskela <miika.koskela@pp3.inet.fi>
 * @copyright Tampereen kaupunginkirjasto - Pirkanmaan maakuntakirjasto
 * @licence MIT-License, see LICENCE-file for more information
 */
/**
 * The purpose of this script is to go through all nodes in MARCXML-dataset and
 * run analyzers on every node.
 *
 * At the moment, only one analyzer can be used at the same time since they all
 * just echo their output.
 *
 * Further development could use a logger, monolog, for example to write the
 * output to different files. This way multiple analyzers could be run at once.
 */
require_once __DIR__ . '/vendor/autoload.php';

use \PIKI\MARCXML\Analytic;
use \PIKI\MARCXML\AnalyzerInterface;
use \PIKI\MARCXML\Analyzer\K653Analyzer;
use \PIKI\MARCXML\Analyzer\K650Analyzer;
use \PIKI\MARCXML\Analyzer\FITamPublishYearAnalyzer;


$analytic = new Analytic;
$analytic->registerAnalyzers(array(
    new FITamPublishYearAnalyzer
));

$iterator = new \DirectoryIterator(__DIR__ . '/data/dump');
foreach($iterator as $item) {

    if(is_dir($item->getPathname())) {
        continue;
    }

    $XMLReader = new \XMLReader;
    $XMLReader->open($item->getPathname(), 'utf-8', LIBXML_NOBLANKS);
    $XMLReader->read();
    $XMLReader->read();

    echo "Analyzing {$item}\n\n";

    do {

        $node = $XMLReader->expand();

        if($node->nodeName === 'records') {
            break;
        }

        $xml = $XMLReader->readOuterXML();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($xml);
        $dom->encoding = 'UTF-8';

        $xpath = new \DOMXpath($dom);
        $xpath->registerNamespace('rd', 'http://www.loc.gov/zing/srw/');
        $xpath->registerNamespace('rc', 'http://www.loc.gov/MARC21/slim');
        $xpath->registerNamespace('php', 'http://php.net/xpath');
        $xpath->registerPhpFunctions();

        // Analyzer instances are registered at the start of this file
        $analytic->run($xpath);

    } while($XMLReader->next());

    $XMLReader->close();
    $analytic->stat();
}

