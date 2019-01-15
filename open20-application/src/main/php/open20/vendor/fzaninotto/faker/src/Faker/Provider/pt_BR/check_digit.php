<?php

namespace Faker\Provider\pt_BR;

/**
 * Calculates one MOD 11 check digit based on customary Brazilian algorithms.
 *
 * @param string|integer $numbers Numbers on which generate the check digit
 * @return integer
 */
function check_digit($numbers)
{
    $length = strlen($numbers);
    $second_algorithm = $length >= 12;
    $verifier = 0;

    for ($i = 1; $i <= $length; $i++) {
        if (!$second_algorithm) {
            $multiplier = $i+1;
        } else {
            $multiplier = ($i >= 9)? $i-7 : $i+1;
        }
        $verifier += $numbers[$length-$i] * $multiplier;
    }

    $verifier = 11 - ($verifier % 11);
    if ($verifier >= 10) {
        $verifier = 0;
    }

    return $verifier;
}
