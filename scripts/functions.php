<?php

/**
 * This file is part of MARCXML-toolset used to handle MARCXML-dataset of PIKI-
 * libraries.
 *
 * Though, these functions are not specific to this particular project.
 *
 * @author Miika Koskela <miika.koskela@pp3.inet.fi>
 * @copyright Tampereen kaupunginkirjasto - Pirkanmaan maakuntakirjasto
 * @licence MIT-License, see LICENCE-file for more information
 */

/**
 * According to Wikipedia article, the median is one of following:
 *
 * - If the number of elements is odd, the middle value when sorted from lowest to highest
 * - If the number of elements is even, then it's the mean of two middle value
 *
 * @see http://en.wikipedia.org/wiki/Median
 * @param array $data
 * @return
 */
function median(array $data) {

    sort($data, SORT_NUMERIC);
    $count = count($data);

    if($count % 2 !== 0) {
        $index = ($count + 1) / 2;
        return $data[$index - 1];
    }

    $half = $count / 2;
    $median = ($data[$half - 1] + $data[$half]) / 2;
    return round($median, 0, PHP_ROUND_HALF_UP);
}

/**
 * Calculate average
 *
 * Assumes the data is numeric
 *
 * @param array $data
 * @return
 */
function average(array $data) {

    $sum    = 0;
    $count  = count($data);

    foreach($data as $year) {
        $sum += $year;
    }

    return round($sum / $count, 0, PHP_ROUND_HALF_UP);
}
