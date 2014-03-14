<?php

/**
 * A MARCXML cleaner tool.
 *
 * The script takes array configuration and loops through all items in it calling
 * remove()-function with current config on a node that is being processed at the
 * moment.
 *
 * Note, that this version is not the latest and it probably won't work.
 *
 * @author Miika Koskela <miika.koskela@pp3.inet.fi>
 * @copyright Tampereen Kaupunginkirjasto - Pirkanmaan maakuntakirjasto
 * @licence MIT-License, see LICENCE.txt for more information
 */

$removals = include __DIR__ . '/config.php';

$counter = 0;
$iterator = new \DirectoryIterator(__DIR__ . '/data/dump');
foreach($iterator as $item) {

    // Skip directories
    if(is_dir($item->getPathname())) {
        continue;
    }

    $writer = new \XMLWriter;
    $writer->openURI(dirname($item->getPathname()) . '/proc/' . $item->getFilename() . '_processed');
    $writer->startDocument('1.0', 'UTF-8');
    $writer->writeRaw('<records xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . "\n");

    $reader = new \XMLReader;
    $reader->open($item->getPathname(), 'UTF-8', LIBXML_NOBLANKS|LIBXML_NSCLEAN);

    echo "Käsitellään: {$item}\n";

    // This advances to the root node
    $reader->read();

    // This dives into the subtree
    $reader->read();

    do {

        $node = $reader->expand();
        if($node->nodeName === 'records') {
            break;
        }

        $xml = $reader->readOuterXML();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($xml);
        $dom->encoding = 'UTF-8';

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('srw', 'http://www.loc.gov/zing/srw/');
        $xpath->registerNamespace('slim', 'http://www.loc.gov/MARC21/slim');

        $identity = identify($node);
        foreach($removals as $removal) {
            remove($xpath, $identity, $removal);
        }

        $writer->writeRaw("{$dom->saveXML()}\n");
        $counter++;

    } while($reader->next());

    $writer->writeRaw('</records>');
    $writer->endDocument();

    $reader->close();

    echo "\nKäsitelty tähän mennessä yhteensä {$counter} tietuetta.\n\n";
}

/**
 * Removes fields.
 *
 * What is removed can be configured through $config. See config.php for examples.
 *
 * @param \DOMElement $node A node to search from
 * @param array $config     A config for removal action
 */
function remove(\DOMXPath $xpath, $identity, array $config) {

    // Config
    $tag        = $config['tag'];
    $codes      = $config['code'];
    $conditions = $config['condition'];
    $remove     = $config['remove'];

    $conditionString = createCondition($conditions);
    $codeString = createCode($codes);

    $query = "//slim:datafield[@tag = '{$tag}']/slim:subfield[{$codeString}{$conditionString}]";
    $nodes = $xpath->query($query);


    if($nodes->length < 1) {
        return;
    }

    // If you loop counting from zero, you'll get null-parents and thus cannot remove
    // nodes.
    $nodeCount = $nodes->length - 1;
    for($i = $nodeCount; $i > 0;  $i--) {

        $item = $nodes->item($i);
        $code = $item->getAttribute('code');

        // Remove subfield
        if($remove === 'subfield') {
            $parent = $item->parentNode;
            if($parent === null) {
                throw new \RuntimeException(
                    'Parent node is null while removing subfield, cannot remove.'
                );
            }

            $value = $item->nodeValue;
            $parent->removeChild($item);
            echo "{$identity}\t{$tag}\t{$code}\tS\t{$value}\n";
        }

        // Remove datafield
        if($remove === 'datafield') {
            $child = $item->parentNode; // i.e. datafield
            if($child === null) {
                throw new \RuntimeException(
                    'Child node is null when removing datafield, cannot remove'
                );
            }

            $parent = $child->parentNode;
            if($parent === null) {
                var_dump($parent, $child);
                throw new \RuntimeException(
                    'Parent node is null when removing datafield, cannot remove'
                );
            }

            $value = $child->nodeValue;
            $parent->removeChild($child);
            echo "{$identity}\t{$tag}\t{$code}\tD\t{$value}\n";
        }


    }
}

/**
 * Creates codition string from given config.
 *
 * @param array $conditions
 * @return string
 */
function createCondition(array $conditions) {

    $conditionString = '';

    foreach($conditions as $condition) {
        $conditionString .= " or contains(., '{$condition}')";
    }

    if(!empty($conditionString)) {
        $conditionString = preg_replace('/^\sor\s/i', '', $conditionString);
        $conditionString = " and ({$conditionString})";
    }

    return $conditionString;
}

/**
 * Creates codes string from given config
 *
 * @param $codes
 * @return string
 */
function createCode($codes) {

    $codeString = '';
    $parts = explode(',', $codes);

    if(count($parts) === 1) {
        return "@code = '{$codes}'";
    }

    foreach($parts as $part) {
        $codeString .= " or @code = '{$part}'";
    }

    $codeString = preg_replace('/^\sor\s/i', '', $codeString);
    return "($codeString)";
}

/**
 * A node can be identified by controlfield which has a tag 001
 *
 * Assuming it's the first one (which can be something else?)
 *
 * @param \DOMElement $element
 * @return string
 */
function identify(\DOMElement $element) {

    // Get the node ID
    $controlfields = $element->getElementsByTagName('controlfield');

    // If there's no controlfields, it's broken
    if($controlfields->length <= 0) {
        throw new \RuntimeException('
            Kontrollikenttiä ei löytynyt.
        ');
    }

    $idElement = $controlfields->item(0);
    if($idElement->getAttribute('tag') !== '001') {
        throw new \RuntimeException('
            Kontrollikenttä 001 puuttuu.
        ');
    }

    $nodeValue = $idElement->nodeValue;
    return $nodeValue;
}

