<?php

/**
 * @param $ssn
 */
function cleanSSN($ssn)
{

    $ssn = preg_replace("/[^0-9]/", "", $ssn);

    $split2 = substr($ssn, 0, 2);

    if ($split2 != 19 && $split2 > date('y')) {
        $ssn = '19' . $ssn;
    } elseif ($split2 != 20 && $split2 <= date('y')) {
        $ssn = '20' . $ssn;
    }

    return $ssn;
}
