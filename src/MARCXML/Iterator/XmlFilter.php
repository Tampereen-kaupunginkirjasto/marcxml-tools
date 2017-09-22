<?php

namespace PIKI\MARCXML\Iterator;

use FilterIterator;

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
