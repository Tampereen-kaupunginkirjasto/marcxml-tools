<?php

namespace PIKI\MARCXML\Iterator;

use FilterIterator;

/**
 * XmlFilter
 *
 * Filters out all but entries ending with .xml
 *
 * @author Miika Koskela
 * @license MIT
 */
class XmlFilter extends FilterIterator
{
    public function accept() : bool
    {
        $current = parent::current();
        if(preg_match("/\.xml$/i", $current))
            return true;
        return false;
    }
}
